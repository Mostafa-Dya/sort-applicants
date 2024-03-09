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
        Schema::create('job_description_tables', function (Blueprint $table) {
            $table->id();
            $table->boolean('status');
            $table->string('category')->nullable();
            $table->string('public_entity')->nullable();
            $table->string('sub_entity')->nullable();
            $table->string('affiliate_entity')->nullable();
            $table->string('sub_affiliate_entity')->nullable();
            $table->integer('gender_needed')->nullable();

            $table->string('general')->nullable();
            $table->string('precise')->nullable();
            $table->string('notes')->nullable();
            $table->string('governorate')->nullable();
            $table->string('job_title')->nullable();
            $table->string('specialization')->nullable();
            $table->string('record_entry')->nullable();
            $table->string('last_modifier')->nullable();

            $table->string('audited_by')->nullable();

            $table->integer('work_centers')->nullable();
            $table->integer('assignees')->nullable();
            $table->integer('vacancies')->nullable();
            $table->integer('card_number')->nullable();


            $table->date('entry_date')->nullable();
            $table->date('modification_date')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_description_tables');
    }
};
