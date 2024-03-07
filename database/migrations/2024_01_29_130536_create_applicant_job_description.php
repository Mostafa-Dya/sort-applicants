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
        Schema::create('applicant_job_description', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_description_id')->constrained('job_description_tables');
            $table->foreignId('applicant_id')->constrained('applicants');

            // Add other necessary fields
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_job_description');
    }
};
