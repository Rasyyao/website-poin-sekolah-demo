<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('year_label'); // e.g. "2026/2027"
            $table->unsignedTinyInteger('semester')->default(1); // 1 = Ganjil, 2 = Genap
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['school_id', 'year_label', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
