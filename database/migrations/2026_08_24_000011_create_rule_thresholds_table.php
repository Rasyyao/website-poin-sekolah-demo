<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rule_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->integer('min_points'); // threshold (accumulated negative points)
            $table->string('action'); // notify_homeroom, notify_parent, call_parent, suspension
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'min_points']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_thresholds');
    }
};
