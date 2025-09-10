<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing records where expires_at is NULL
        DB::table('user_verifications')
            ->whereNull('expires_at')
            ->update(['expires_at' => now()->addMinutes(10)]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally, set expires_at back to NULL
        DB::table('user_verifications')
            ->whereNotNull('expires_at')
            ->update(['expires_at' => null]);
    }
};
