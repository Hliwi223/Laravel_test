<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('project_type');                       // evenement, chantier, stand, autre
            $table->unsignedInteger('total_energy_wh');
            $table->unsignedInteger('energy_with_margin_wh');      // énergie système (marge + rendement inclus)
            $table->unsignedInteger('max_simultaneous_power_w');
            $table->unsignedInteger('peak_power_w');
            $table->unsignedInteger('recommended_battery_wh')->nullable();
            $table->unsignedInteger('recommended_battery_ah')->nullable();
            $table->unsignedInteger('battery_voltage')->nullable(); // 12 / 24 / 48
            $table->string('battery_chemistry')->nullable();        // lithium / plomb
            $table->unsignedInteger('recommended_inverter_w')->nullable();
            $table->unsignedInteger('recommended_solar_w')->nullable();
            $table->foreignId('recommended_pack_id')->nullable()->constrained('packs')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
