<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculatorRequest;
use App\Models\Appliance;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Services\EnergyCalculatorService;
use App\Services\PackRecommendationService;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function __construct(
        private EnergyCalculatorService $calculator,
        private PackRecommendationService $recommender,
    ) {
    }

    /** Formulaire du calculateur (assistant étape par étape). */
    public function index()
    {
        $appliances = Appliance::orderBy('category')->orderBy('name')->get();
        $params = $this->calculator->params();

        return view('calculator.index', compact('appliances', 'params'));
    }

    /** Traite le calcul, sauvegarde le devis et affiche le résultat. */
    public function calculate(CalculatorRequest $request)
    {
        $data = $request->validated();

        // Compléter les infos depuis la bibliothèque si appliance_id fourni
        $items = [];
        foreach ($data['appliances'] as $row) {
            $appliance = !empty($row['appliance_id']) ? Appliance::find($row['appliance_id']) : null;

            $items[] = [
                'appliance_id' => $appliance?->id,
                'name' => $row['name'],
                'power_w' => $row['power_w'],
                'quantity' => $row['quantity'],
                'duration_hours' => $row['duration_hours'],
                'usage_period' => $row['usage_period'],
                'has_startup_surge' => (bool) $row['has_startup_surge'],
                'startup_factor' => $appliance?->startup_factor,
            ];
        }

        $calculation = $this->calculator->calculate($items, [
            'battery_chemistry' => $data['battery_chemistry'] ?? 'lithium',
            'battery_voltage' => $data['battery_voltage'] ?? 24,
        ]);

        $recommendations = $this->recommender->recommend($calculation);

        // Sauvegarde du devis + ses lignes
        $quote = Quote::create([
            'project_type' => $data['project_type'],
            'total_energy_wh' => $calculation['totals']['total_energy_wh'],
            'energy_with_margin_wh' => $calculation['totals']['system_energy_wh'],
            'max_simultaneous_power_w' => $calculation['totals']['max_simultaneous_power_w'],
            'peak_power_w' => $calculation['totals']['peak_power_w'],
            'recommended_battery_wh' => $calculation['recommendations']['battery_wh'],
            'recommended_battery_ah' => $calculation['recommendations']['battery_ah'],
            'battery_voltage' => $calculation['recommendations']['battery_voltage'],
            'battery_chemistry' => $calculation['recommendations']['battery_chemistry'],
            'recommended_inverter_w' => $calculation['recommendations']['inverter_w'],
            'recommended_solar_w' => $calculation['recommendations']['solar_w'],
            'recommended_pack_id' => $recommendations['best']?->id,
        ]);

        foreach ($items as $i => $item) {
            QuoteItem::create([
                'quote_id' => $quote->id,
                'appliance_id' => $item['appliance_id'],
                'name' => $item['name'],
                'power_w' => (int) $item['power_w'],
                'quantity' => (int) $item['quantity'],
                'duration_hours' => (float) $item['duration_hours'],
                'usage_period' => $item['usage_period'],
                'has_startup_surge' => $item['has_startup_surge'],
                'startup_power_w' => $calculation['items'][$i]['startup_power_w'],
                'energy_wh' => $calculation['items'][$i]['energy_wh'],
            ]);
        }

        return view('calculator.result', [
            'quote' => $quote,
            'calculation' => $calculation,
            'recommendations' => $recommendations,
        ]);
    }

    /** Réaffichage d'un devis sauvegardé. */
    public function show(Quote $quote)
    {
        $calculation = $this->calculator->calculate(
            $quote->items->map(fn ($it) => [
                'name' => $it->name,
                'power_w' => $it->power_w,
                'quantity' => $it->quantity,
                'duration_hours' => $it->duration_hours,
                'usage_period' => $it->usage_period,
                'has_startup_surge' => $it->has_startup_surge,
            ])->all(),
            [
                'battery_chemistry' => $quote->battery_chemistry,
                'battery_voltage' => $quote->battery_voltage,
            ]
        );

        $recommendations = $this->recommender->recommend($calculation);

        return view('calculator.result', compact('quote', 'calculation', 'recommendations'));
    }

    /** API JSON pour le formulaire dynamique (liste des appareils de la bibliothèque). */
    public function appliancesJson()
    {
        return response()->json(
            Appliance::orderBy('name')->get([
                'id', 'name', 'default_power_w', 'default_duration_hours',
                'has_startup_surge', 'startup_factor', 'category',
            ])
        );
    }
}
