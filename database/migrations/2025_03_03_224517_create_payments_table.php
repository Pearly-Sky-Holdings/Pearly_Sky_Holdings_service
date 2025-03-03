<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId(column: 'order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // paypal, stripe
            $table->string('status'); // pending, completed, failed, cancelled
            $table->string('transaction_id')->nullable(); // Payment gateway reference ID
            $table->text('payment_data')->nullable(); // JSON data with payment details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}