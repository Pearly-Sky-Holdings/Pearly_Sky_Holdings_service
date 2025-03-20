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
            $table->foreignId(column: 'order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services', 'service_id')->onDelete('cascade');
            $table->string('price');
            $table->date('date');
            $table->time('time');
            $table->string('property_size')->nullable();
            $table->string('duration')->nullable();  // Duration in minutes
            $table->integer('number_of_cleaners')->default(1);
            $table->string('business_property')->nullable();
            $table->string('frequency')->nullable();
            $table->string('request_gender')->nullable();
            $table->string('request_language')->default('en');
            $table->string('cleaning_solvents')->nullable();
            $table->string('Equipment')->nullable();
            $table->string('free_estimate')->nullable();
            $table->string('time_zoon')->nullable();
            $table->string('special_request')->nullable();

            $table->string('chemical')->nullable();
            $table->string('things_to_clean')->nullable();
            $table->string('location_from')->nullable();
            $table->string('location_to')->nullable();
            $table->string('materials')->nullable();
            $table->string('options_type')->nullable();
            $table->string('pressure_washing_type')->nullable();
            $table->string('event_type')->nullable();
            $table->string('pool_type')->nullable();


            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'confirmed', 'cancelled'])->default('pending');
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
