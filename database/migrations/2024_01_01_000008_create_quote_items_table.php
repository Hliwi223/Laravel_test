<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appliance_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('power_w');
            $table->unsignedInteger('quantity');
            $table->float('duration_hours');
            $table->enum('usage_period', ['jour', 'nuit', 'jour_nuit'])->default('jour_nuit');
            $table->boolean('has_startup_surge')->default(false);
            $table->unsignedInteger('startup_power_w')->nullable();
            $table->unsignedInteger('energy_wh');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
