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
        Schema::table('time_frames', function (Blueprint $table) {
            $table->integer('hourly_rate')->nullable();
            $table->string('currency', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_frames', function (Blueprint $table) {
            $table->dropColumn('hourly_rate');
            $table->dropColumn('currency');
        });
    }
};
