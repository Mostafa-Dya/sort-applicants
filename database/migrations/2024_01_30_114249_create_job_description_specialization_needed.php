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
        Schema::create('job_description_specialization_needed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_description_id')->constrained('job_description_tables');
            $table->string('degree');
            $table->string('specialization_needed');
            $table->string('specialization_needed_precise')->nullable();

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_description_specialization_needed');
    }
};
