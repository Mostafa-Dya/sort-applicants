<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->date('birthDate')->nullable();
            $table->integer('cardNumber')->nullable();
            $table->string('category');
            $table->json('certificate'); 
            $table->string('desiredGovernorate')->nullable();
            $table->string('destination')->nullable();
            $table->integer('desireOrder')->nullable();
            $table->string('exactSpecialization')->nullable();
            $table->string('fullName')->nullable();
            $table->string('governorate');
            $table->string('graduationDate')->nullable();
            $table->decimal('graduationRate', 8, 3)->nullable(); 
            $table->string('idNumber')->nullable();
            $table->string('institute')->nullable();
            $table->string('motherName')->nullable();
            $table->string('residence')->nullable();
            $table->string('the_ministry')->nullable();
            $table->string('notes')->nullable();
            $table->string('named')->nullable();
            $table->string('sub_entity')->nullable();

            // $table->integer('series')->nullable();
            
            $table->string('lastModifier');
            $table->date('modificationDate');
            $table->string('recordEntry')->nullable();
            $table->date('entryDate');
            $table->boolean('gender')->nullable();

            $table->boolean('status')->default(false);
            $table->boolean('accepted')->default(false);

            $table->integer('exam_result')->nullable();
            
            $table->string('reason')->nullable();




            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
