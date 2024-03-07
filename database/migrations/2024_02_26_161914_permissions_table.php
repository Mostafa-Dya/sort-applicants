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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');

            $table->boolean('canActive');

            $table->boolean('statusCheck');
            $table->boolean('sortable');

            $table->boolean('addApplicants');
            $table->boolean('addCertificate');
            $table->boolean('addJobDescription');
            $table->boolean('addGovernorate');
            $table->boolean('addPublic');

            $table->boolean('editApplicants');
            $table->boolean('editCertificate');
            $table->boolean('editJobDescription');
            $table->boolean('editGovernorate');
            $table->boolean('editPublic');

            $table->boolean('deleteApplicants');
            $table->boolean('deleteCertificate');
            $table->boolean('deleteJobDescription');
            $table->boolean('deleteGovernorate');
            $table->boolean('deletePublic');

            // Add other necessary fields
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
