<?php

namespace App\Services;

use App\Models\Pack;

/**
 * Recherche les packs qui couvrent réellement les besoins calculés :
 *  énergie du pack >= énergie nécessaire (avec marge, avant DoD)
 *  ET puissance continue onduleur >= puissance simultanée nécessaire
 *  ET surge >= puissance de crête.
 *
 * Retourne jusqu'à 3 classements : meilleur choix, option économique, option supérieure.
 */
class PackRecommendationService
{
    public function recommend(array $calculation): array
    {
        $neededEnergy = $calculation['totals']['energy_with_margin_wh'];
        $neededPower = $calculation['totals']['max_simultaneous_power_w'];
        $neededPeak = $calculation['totals']['peak_power_w'];

        $matching = Pack::query()
            ->where('is_active', true)
            ->where('energy_capacity_wh', '>=', $neededEnergy)
            ->where('continuous_power_w', '>=', $neededPower)
            ->where('surge_power_w', '>=', $neededPeak)
            ->orderBy('price_per_day')
            ->get();

        if ($matching->isEmpty()) {
            return ['best' => null, 'economy' => null, 'premium' => null, 'count' => 0];
        }

        // Option économique = le moins cher qui couvre les besoins
        $economy = $matching->first();

        // Meilleur choix = couverture la plus proche des besoins sans surdimensionnement excessif
        $best = $matching->sortBy(function (Pack $pack) use ($neededEnergy, $neededPower) {
            $energyRatio = $neededEnergy > 0 ? $pack->energy_capacity_wh / $neededEnergy : 1;
            $powerRatio = $neededPower > 0 ? $pack->continuous_power_w / $neededPower : 1;
            // pénalise le surdimensionnement et le prix
            return ($energyRatio + $powerRatio) * 0.5 + ($pack->price_per_day / 1000);
        })->first();

        // Option supérieure = le pack au-dessus (plus grande capacité), si différent
        $premium = $matching->sortByDesc('energy_capacity_wh')->first();
        if ($premium && $economy && $premium->id === $economy->id && $matching->count() < 2) {
            $premium = null;
        }

        return [
            'best' => $best,
            'economy' => $economy,
            'premium' => $premium,
            'count' => $matching->count(),
        ];
    }
}
