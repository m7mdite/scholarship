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
        Schema::create('application_criterias', function (Blueprint $table) {
            $table->id();
            $table->string('requirment_type', 20);
            $table->string('application_criteria_value', 30);
            $table->string('application_criteria_description', 100)->nullable();
            $table->foreignId('scholarship_id')->constrained('scholarships')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_criterias');
    }
};
