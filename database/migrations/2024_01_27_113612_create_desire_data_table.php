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
        Schema::create('desire_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade'); 
            $table->string('governorateDesire');
            $table->string('publicEntitySide');
            $table->string('cardNumberDesire');
            $table->string('publicEntity');
            $table->string('numberOfCenters');
            $table->string('jobTitle');
            $table->string('primarySpecialization');
            $table->string('specifiedSpecialization');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desire_data');
    }
};
