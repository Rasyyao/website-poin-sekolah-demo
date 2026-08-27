<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nisn'); // Nomor Induk Siswa Nasional
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('parent_contact')->nullable(); // WA number or email
            $table->string('access_code')->nullable(); // hashed, for parent login
            $table->timestamps();

            $table->unique(['school_id', 'nisn']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
