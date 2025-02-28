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
        Schema::create('re_stocking_checklist_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('re_stocking_checklist_id')->constrained('re_stocking_checklists', 'id')->onDelete('cascade');
            $table->foreignId('service_detail_id')->constrained('service_details', 'id')->onDelete('cascade');            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('re_stocking_checklist_details');
    }
};
