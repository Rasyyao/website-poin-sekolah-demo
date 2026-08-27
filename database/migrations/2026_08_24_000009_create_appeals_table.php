<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('points_log_id')->constrained('points_log')->cascadeOnDelete();
            $table->string('submitter_type'); // App\Models\User or App\Models\Student
            $table->unsignedBigInteger('submitter_id');
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamps();

            $table->index(['submitter_type', 'submitter_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeals');
    }
};
