<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('recommended_uses')->nullable(); // usages recommandés (texte libre)
            $table->decimal('price_per_day', 8, 2);       // TND / jour
            $table->decimal('deposit', 8, 2)->default(0);
            $table->unsignedInteger('energy_capacity_wh');   // énergie disponible du pack
            $table->unsignedInteger('continuous_power_w');   // puissance continue onduleur
            $table->unsignedInteger('surge_power_w');        // surge rating onduleur
            $table->unsignedInteger('solar_power_w')->nullable();
            $table->unsignedInteger('quantity')->default(1); // nombre d'exemplaires du pack
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packs');
    }
};
