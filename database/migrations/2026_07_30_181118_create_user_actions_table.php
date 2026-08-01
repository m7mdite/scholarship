<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // مثل: ai_enhance_bio, generate_cv, generate_motivation
            $table->json('metadata')->nullable(); // بيانات إضافية (اختياري)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_actions');
    }
};