<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rule_id')->constrained()->restrictOnDelete();
            $table->foreignId('reported_by')->constrained('users')->restrictOnDelete();
            $table->integer('points'); // snapshot from rules.points at time of recording
            $table->string('evidence_url')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->default('approved'); // pending, approved, rejected
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['school_id', 'student_id']);
            $table->index(['school_id', 'status']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_log');
    }
};
