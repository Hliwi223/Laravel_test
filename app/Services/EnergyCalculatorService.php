<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Logique pure de calcul énergétique du calculateur.
 * Aucune dépendance HTTP : testable unitairement.
 *
 * Paramètres par défaut (modifiables via la table settings) :
 *  - rendement système   = 0.85
 *  - marge énergie       = 20 %
 *  - DoD lithium         = 80 %
 *  - DoD plomb           = 50 %
 *  - heures de soleil    = 4.5 h/jour
 *  - facteur onduleur    = 1.25
 *  - facteur pic moteur  = 2x
 */
class EnergyCalculatorService
{
    public function params(): array
    {
        return [
            'efficiency'      => (float) Setting::get('default_system_efficiency', 0.85),
            'margin'          => (float) Setting::get('default_energy_margin', 0.20),
            'dod_lithium'     => (float) Setting::get('default_lithium_dod', 0.80),
            'dod_lead'        => (float) Setting::get('default_lead_dod', 0.50),
            'sun_hours'       => (float) Setting::get('default_solar_hours', 4.5),
            'inverter_factor' => (float) Setting::get('default_inverter_factor', 1.25),
            'surge_factor'    => (float) Setting::get('default_motor_surge_factor', 2.0),
        ];
    }

    /**
     * @param array $items chaque item : [name, power_w, quantity, duration_hours,
     *                     usage_period, has_startup_surge, startup_factor?]
     * @param array $options [battery_chemistry => lithium|plomb, battery_voltage => 12|24|48]
     */
    public function calculate(array $items, array $options = []): array
    {
        $p = $this->params();

        $chemistry = ($options['battery_chemistry'] ?? 'lithium') === 'plomb' ? 'plomb' : 'lithium';
        $voltage = in_array((int) ($options['battery_voltage'] ?? 24), [12, 24, 48], true)
            ? (int) $options['battery_voltage']
            : 24;
        $dod = $chemistry === 'plomb' ? $p['dod_lead'] : $p['dod_lithium'];

        $totalEnergy = 0.0;
        $maxSimultaneous = 0.0;
        $peakPower = 0.0;
        $computedItems = [];

        foreach ($items as $item) {
            $power = (float) $item['power_w'];
            $qty = (float) $item['quantity'];
            $duration = (float) $item['duration_hours'];
            $hasSurge = !empty($item['has_startup_surge']);
            $factor = (float) ($item['startup_factor'] ?? $p['surge_factor']);

            // Énergie = Puissance x Quantité x Durée
            $energy = $power * $qty * $duration;
            $totalEnergy += $energy;

            // Puissance simultanée de cet appareil (toutes unités en même temps)
            $simultaneous = $power * $qty;
            if ($simultaneous > $maxSimultaneous) {
                $maxSimultaneous = $simultaneous;
            }

            // Pic de démarrage
            $startupPower = $hasSurge ? $simultaneous * $factor : $simultaneous;
            if ($startupPower > $peakPower) {
                $peakPower = $startupPower;
            }

            $computedItems[] = [
                'name'              => $item['name'],
                'power_w'           => $power,
                'quantity'          => (int) $qty,
                'duration_hours'    => $duration,
                'usage_period'      => $item['usage_period'] ?? 'jour_nuit',
                'has_startup_surge' => $hasSurge,
                'startup_power_w'   => (int) round($startupPower),
                'energy_wh'         => (int) round($energy),
            ];
        }

        // Marge de sécurité puis rendement système (le rendement n'est compté qu'une fois ici)
        $energyWithMargin = $totalEnergy * (1 + $p['margin']);
        $systemEnergy = $energyWithMargin / $p['efficiency'];

        // Batterie : capacité à stocker selon le DoD choisi
        $batteryWh = $systemEnergy / $dod;
        $batteryAh = $batteryWh / $voltage;

        // Onduleur : puissance continue recommandée avec facteur de sécurité
        $inverterW = $maxSimultaneous * $p['inverter_factor'];

        // Panneaux : puissance à installer pour produire l'énergie sur les heures de soleil
        // (rendement déjà inclus dans systemEnergy -> on ne divise que par les heures de soleil
        //  pour éviter de compter deux fois le rendement ; on reste cohérent avec la formule
        //  demandée energy/(sun*eff) appliquée à l'énergie AVEC marge uniquement)
        $solarW = $energyWithMargin / ($p['sun_hours'] * $p['efficiency']);

        return [
            'items' => $computedItems,
            'params_used' => $p + ['dod' => $dod, 'chemistry' => $chemistry, 'voltage' => $voltage],
            'totals' => [
                'total_energy_wh' => (int) round($totalEnergy),
                'energy_with_margin_wh' => (int) round($energyWithMargin),
                'system_energy_wh' => (int) round($systemEnergy),
                'max_simultaneous_power_w' => (int) round($maxSimultaneous),
                'peak_power_w' => (int) round($peakPower),
            ],
            'recommendations' => [
                'battery_wh' => (int) ceil($batteryWh / 100) * 100,   // arrondi ~100 Wh près
                'battery_ah' => (int) ceil($batteryAh),               // arrondi à l'Ah supérieur
                'battery_voltage' => $voltage,
                'battery_chemistry' => $chemistry,
                'inverter_w' => $this->roundInverterSize($inverterW), // puissance "standard" disponible
                'inverter_raw_w' => (int) round($inverterW),
                'surge_w' => (int) ceil($peakPower / 500) * 500,      // surge rating standard
                'solar_w' => (int) ceil($solarW / 100) * 100,         // arrondi ~100 Wc près
            ],
        ];
    }

    /**
     * Arrondit la puissance onduleur vers une taille réellement commercialisée.
     */
    private function roundInverterSize(float $w): int
    {
        $sizes = [600, 1000, 1200, 1500, 2000, 2500, 3000, 4000, 5000, 6000, 8000, 10000, 12000];
        foreach ($sizes as $size) {
            if ($w <= $size) {
                return $size;
            }
        }
        return (int) ceil($w / 1000) * 1000;
    }
}
