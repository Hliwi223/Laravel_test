<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pack_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('cin', 20)->nullable(); // donnée sensible, optionnelle dans le MVP
            $table->string('project_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location');
            $table->string('address')->nullable();
            $table->boolean('delivery_required')->default(false);
            $table->decimal('delivery_fee', 8, 2)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'rejected'])
                  ->default('pending');
            $table->decimal('estimated_price', 8, 2)->nullable(); // total location TND
            $table->decimal('deposit_amount', 8, 2)->nullable();  // caution TND
            $table->text('customer_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
