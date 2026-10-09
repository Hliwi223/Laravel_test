<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        // Suivi de disponibilité par produit/pack et par date (empêche les doublons)
        Schema::create('availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('pack_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('quantity_total');
            $table->unsignedInteger('quantity_reserved')->default(0);
            $table->unsignedInteger('quantity_available');
            $table->timestamps();

            $table->unique(['product_id', 'pack_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability');
        Schema::dropIfExists('reservation_products');
    }
};
