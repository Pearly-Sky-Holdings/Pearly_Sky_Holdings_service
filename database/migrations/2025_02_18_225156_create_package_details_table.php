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
        Schema::create('package_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages', 'package_id')->onDelete('cascade');
            $table->foreignId('service_detail_id')->constrained('service_details', 'id')->onDelete('cascade');
            $table->string('price',)->default('0$')->nullable();
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_details');
    }
};
