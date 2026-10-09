<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appliances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('default_power_w');
            $table->float('default_duration_hours')->default(1);
            $table->boolean('has_startup_surge')->default(false);
            $table->float('startup_factor')->default(2.0); // pic = puissance x facteur
            $table->string('category')->nullable();       // événement / chantier / commun
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appliances');
    }
};
