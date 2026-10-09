<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('power_w')->nullable();          // puissance W (NULL si non applicable)
            $table->unsignedInteger('voltage_v')->nullable();
            $table->unsignedInteger('capacity_wh')->nullable();
            $table->unsignedInteger('capacity_ah')->nullable();
            $table->unsignedInteger('surge_power_w')->nullable();     // pic de puissance supported
            $table->decimal('daily_price', 8, 2);                     // TND / jour
            $table->decimal('deposit', 8, 2)->default(0);             // caution TND
            $table->unsignedInteger('quantity')->default(1);          // quantité totale en stock
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
