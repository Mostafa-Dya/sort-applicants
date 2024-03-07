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
        Schema::create('scientific_certificate_precise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_general_id')->constrained('scientific_certificate_general')->onDelete('cascade');
            $table->string('name');
            $table->integer('category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scientific_certificate_precise');
    }
};
