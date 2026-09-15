<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rule_thresholds', function (Blueprint $table) {
            $table->string('type')->default('violation')->after('school_id'); // violation, achievement
            $table->index(['school_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::table('rule_thresholds', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'type']);
            $table->dropColumn('type');
        });
    }
};
