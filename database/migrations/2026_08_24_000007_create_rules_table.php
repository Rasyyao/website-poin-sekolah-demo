<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // violation, achievement
            $table->string('category')->nullable(); // ringan, sedang, berat (null for achievements)
            $table->integer('points'); // negative for violations, positive for achievements
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};
