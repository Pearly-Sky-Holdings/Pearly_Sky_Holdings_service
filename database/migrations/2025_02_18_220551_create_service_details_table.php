<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_details', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services', 'service_id')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->date('date');
            $table->time('time');
            $table->string('property_size')->nullable();
            $table->integer('duration')->nullable();  // Duration in minutes
            $table->integer('number_of_cleaners')->default(1);
            $table->text('note')->nullable();
            $table->string('person_type')->nullable();
            $table->string('language')->default('en');
            $table->string('business_property')->nullable();
            $table->string('cleaning_solvents')->nullable();
            $table->string('Equipment')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_details');
    }
};
