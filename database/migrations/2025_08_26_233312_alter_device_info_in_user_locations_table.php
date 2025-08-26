<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_locations', function (Blueprint $table) {
            // Change from string(255) → text (no limit issues)
            $table->text('device_info')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_locations', function (Blueprint $table) {
            // Rollback to string(255)
            $table->string('device_info', 255)->nullable()->change();
        });
    }
};
