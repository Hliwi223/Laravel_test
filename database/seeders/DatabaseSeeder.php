<?php

namespace Database\Seeders;

use App\Models\Appliance;
use App\Models\Category;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Catégories ----------
        $categories = [
            'Panneaux' => 'Panneaux solaires photovoltaïques',
            'Batteries' => 'Batteries lithium et plomb-gel',
            'Onduleurs' => 'Onduleurs pur sinus avec pic de surcharge',
            'Chargeurs / MPPT' => 'Régulateurs MPPT et chargeurs',
            'Accessoires' => 'Câbles, connecteurs, protections',
            'Générateurs' => 'Groupes électrogènes (option)',
        ];

        $catIds = [];
        foreach ($categories as $name => $description) {
            $catIds[$name] = Category::create(['name' => $name, 'description' => $description])->id;
        }

        // ---------- Produits ----------
        $products = [
            // Panneaux
            ['cat' => 'Panneaux', 'name' => 'Panneau solaire 200 W monocristallin', 'ref' => 'PAN-200', 'power_w' => 200, 'price' => 15, 'deposit' => 300, 'qty' => 10],
            ['cat' => 'Panneaux', 'name' => 'Panneau solaire 400 W monocristallin', 'ref' => 'PAN-400', 'power_w' => 400, 'price' => 25, 'deposit' => 600, 'qty' => 8],
            ['cat' => 'Panneaux', 'name' => 'Panneau solaire 550 W haute performance', 'ref' => 'PAN-550', 'power_w' => 550, 'price' => 35, 'deposit' => 800, 'qty' => 6],
            // Batteries
            ['cat' => 'Batteries', 'name' => 'Batterie Lithium LiFePO4 12V 100Ah', 'ref' => 'BAT-L12100', 'voltage_v' => 12, 'capacity_wh' => 1200, 'capacity_ah' => 100, 'price' => 30, 'deposit' => 900, 'qty' => 8],
            ['cat' => 'Batteries', 'name' => 'Batterie Lithium LiFePO4 24V 100Ah', 'ref' => 'BAT-L24100', 'voltage_v' => 24, 'capacity_wh' => 2400, 'capacity_ah' => 100, 'price' => 50, 'deposit' => 1500, 'qty' => 6],
            ['cat' => 'Batteries', 'name' => 'Batterie Gel plomb 12V 200Ah', 'ref' => 'BAT-G12200', 'voltage_v' => 12, 'capacity_wh' => 2400, 'capacity_ah' => 200, 'price' => 35, 'deposit' => 700, 'qty' => 5],
            // Onduleurs
            ['cat' => 'Onduleurs', 'name' => 'Onduleur pur sinus 1000 W / 2000 W surge', 'ref' => 'OND-1000', 'power_w' => 1000, 'surge_power_w' => 2000, 'voltage_v' => 24, 'price' => 25, 'deposit' => 600, 'qty' => 6],
            ['cat' => 'Onduleurs', 'name' => 'Onduleur pur sinus 2000 W / 4000 W surge', 'ref' => 'OND-2000', 'power_w' => 2000, 'surge_power_w' => 4000, 'voltage_v' => 24, 'price' => 40, 'deposit' => 1000, 'qty' => 5],
            ['cat' => 'Onduleurs', 'name' => 'Onduleur pur sinus 3000 W / 6000 W surge', 'ref' => 'OND-3000', 'power_w' => 3000, 'surge_power_w' => 6000, 'voltage_v' => 48, 'price' => 60, 'deposit' => 1500, 'qty' => 3],
            // Chargeurs / MPPT
            ['cat' => 'Chargeurs / MPPT', 'name' => 'Régulateur MPPT 30 A 12/24 V', 'ref' => 'MPPT-30', 'power_w' => 800, 'voltage_v' => 24, 'price' => 12, 'deposit' => 250, 'qty' => 8],
            ['cat' => 'Chargeurs / MPPT', 'name' => 'Régulateur MPPT 60 A 24/48 V', 'ref' => 'MPPT-60', 'power_w' => 1600, 'voltage_v' => 48, 'price' => 20, 'deposit' => 400, 'qty' => 4],
            // Accessoires
            ['cat' => 'Accessoires', 'name' => 'Câblage solaire + connecteurs MC4 (kit 10 m)', 'ref' => 'ACC-CAB10', 'price' => 5, 'deposit' => 50, 'qty' => 20],
            ['cat' => 'Accessoires', 'name' => 'Coffret DC disjoncteurs + parafoudre', 'ref' => 'ACC-BOITE', 'price' => 8, 'deposit' => 150, 'qty' => 10],
            // Générateurs
            ['cat' => 'Générateurs', 'name' => 'Générateur essence 2 kW (appoint)', 'ref' => 'GEN-2KW', 'power_w' => 2000, 'price' => 70, 'deposit' => 1200, 'qty' => 2],
        ];

        $productIds = [];
        foreach ($products as $p) {
            $model = Product::create([
                'category_id' => $catIds[$p['cat']],
                'name' => $p['name'],
                'reference' => $p['ref'],
                'description' => 'Matériel professionnel adapté à la location temporaire en Tunisie.',
                'power_w' => $p['power_w'] ?? null,
                'voltage_v' => $p['voltage_v'] ?? null,
                'capacity_wh' => $p['capacity_wh'] ?? null,
                'capacity_ah' => $p['capacity_ah'] ?? null,
                'surge_power_w' => $p['surge_power_w'] ?? null,
                'daily_price' => $p['price'],
                'deposit' => $p['deposit'],
                'quantity' => $p['qty'],
                'is_active' => true,
            ]);
            $productIds[$p['ref']] = $model->id;
        }

        // ---------- Packs ----------
        $packStands = Pack::create([
            'name' => 'Pack Stand Marché',
            'description' => 'Solution compacte pour stand, marché ou petit point de vente : énergie silencieuse et propre pour une journée.',
            'recommended_uses' => 'Stand, marché, petite boutique, présentation extérieure',
            'price_per_day' => 60,
            'deposit' => 800,
            'energy_capacity_wh' => 1200,
            'continuous_power_w' => 600,
            'surge_power_w' => 1200,
            'solar_power_w' => 200,
            'quantity' => 4,
            'is_active' => true,
        ]);
        $packStands->products()->sync([
            $productIds['BAT-L12100'] => ['quantity' => 1],
            $productIds['PAN-200'] => ['quantity' => 1],
            $productIds['OND-1000'] => ['quantity' => 1],
            $productIds['ACC-CAB10'] => ['quantity' => 1],
        ]);

        $packEvent = Pack::create([
            'name' => 'Pack Événement 1 Nuit',
            'description' => 'Éclairage, sonorisation, TV et petits équipements pour une soirée complète (mariage, anniversaire, concert).',
            'recommended_uses' => 'Mariage, anniversaire, festival, événement en plein air, DJ',
            'price_per_day' => 150,
            'deposit' => 2500,
            'energy_capacity_wh' => 5000,
            'continuous_power_w' => 2000,
            'surge_power_w' => 4000,
            'solar_power_w' => 1200,
            'quantity' => 3,
            'is_active' => true,
        ]);
        $packEvent->products()->sync([
            $productIds['BAT-L24100'] => ['quantity' => 2],
            $productIds['PAN-400'] => ['quantity' => 3],
            $productIds['OND-2000'] => ['quantity' => 1],
            $productIds['MPPT-30'] => ['quantity' => 1],
            $productIds['ACC-CAB10'] => ['quantity' => 2],
            $productIds['ACC-BOITE'] => ['quantity' => 1],
        ]);

        $packSite = Pack::create([
            'name' => 'Pack Chantier Journée',
            'description' => "Alimentation d'outils électriques avec pics de démarrage (perceuse, meuleuse), éclairage et bureau temporaire.",
            'recommended_uses' => 'Chantier, bureau temporaire, outils électroportatifs, pompe',
            'price_per_day' => 180,
            'deposit' => 3000,
            'energy_capacity_wh' => 7200,
            'continuous_power_w' => 3000,
            'surge_power_w' => 6000,
            'solar_power_w' => 1100,
            'quantity' => 2,
            'is_active' => true,
        ]);
        $packSite->products()->sync([
            $productIds['BAT-L24100'] => ['quantity' => 3],
            $productIds['PAN-550'] => ['quantity' => 2],
            $productIds['OND-3000'] => ['quantity' => 1],
            $productIds['MPPT-60'] => ['quantity' => 1],
            $productIds['ACC-CAB10'] => ['quantity' => 2],
            $productIds['ACC-BOITE'] => ['quantity' => 1],
        ]);

        // ---------- Bibliothèque d'appareils (calculateur) ----------
        $appliances = [
            ['name' => 'Éclairage LED', 'power' => 20, 'dur' => 5, 'surge' => false, 'cat' => 'Événement'],
            ['name' => 'Enceinte audio', 'power' => 100, 'dur' => 4, 'surge' => false, 'cat' => 'Événement'],
            ['name' => 'Table de mixage DJ', 'power' => 300, 'dur' => 5, 'surge' => false, 'cat' => 'Événement'],
            ['name' => 'Télévision', 'power' => 120, 'dur' => 4, 'surge' => false, 'cat' => 'Événement'],
            ['name' => 'Réfrigérateur', 'power' => 150, 'dur' => 6, 'surge' => true, 'factor' => 3, 'cat' => 'Événement'],
            ['name' => 'Ventilateur', 'power' => 60, 'dur' => 6, 'surge' => true, 'factor' => 2, 'cat' => 'Événement'],
            ['name' => 'Perceuse', 'power' => 800, 'dur' => 2, 'surge' => true, 'factor' => 2, 'cat' => 'Chantier'],
            ['name' => 'Meuleuse', 'power' => 1000, 'dur' => 1, 'surge' => true, 'factor' => 2, 'cat' => 'Chantier'],
            ['name' => 'Pompe', 'power' => 750, 'dur' => 3, 'surge' => true, 'factor' => 3, 'cat' => 'Chantier'],
            ['name' => 'Ordinateur', 'power' => 100, 'dur' => 8, 'surge' => false, 'cat' => 'Chantier'],
            ['name' => 'Imprimante', 'power' => 200, 'dur' => 1, 'surge' => false, 'cat' => 'Chantier'],
            ['name' => 'Éclairage chantier', 'power' => 100, 'dur' => 8, 'surge' => false, 'cat' => 'Chantier'],
            ['name' => 'Chargeur téléphone', 'power' => 15, 'dur' => 3, 'surge' => false, 'cat' => 'Commun'],
            ['name' => 'Ventilateur de stand', 'power' => 50, 'dur' => 8, 'surge' => false, 'cat' => 'Commun'],
        ];

        foreach ($appliances as $a) {
            Appliance::create([
                'name' => $a['name'],
                'default_power_w' => $a['power'],
                'default_duration_hours' => $a['dur'],
                'has_startup_surge' => $a['surge'],
                'startup_factor' => $a['factor'] ?? 2.0,
                'category' => $a['cat'],
            ]);
        }

        // ---------- Paramètres globaux ----------
        $defaults = [
            'whatsapp_number' => '21612345678', // numéro fictif à modifier dans l'admin
            'default_system_efficiency' => '0.85',
            'default_energy_margin' => '0.20',
            'default_solar_hours' => '4.5',
            'default_inverter_factor' => '1.25',
            'default_lithium_dod' => '0.80',
            'default_lead_dod' => '0.50',
            'default_motor_surge_factor' => '2.0',
            'default_delivery_fee' => '30',
        ];
        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // ---------- Compte administrateur de test ----------
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'), // mot de passe de TEST uniquement, à changer en production
                'role' => 'admin',
                'is_active' => true,
                'phone' => '+216 00 000 000',
            ]
        );
    }
}
