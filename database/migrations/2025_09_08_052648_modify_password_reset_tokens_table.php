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
        // Step 1: Drop primary key on email
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropPrimary();
        });

        // Step 2: Modify columns and add id
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Make email nullable
            $table->string('email', 191)->nullable()->change();

            // Make token nullable (important for phone+otp flow)
            $table->string('token', 191)->nullable()->change();

            // Add id column if it does not exist
            if (!Schema::hasColumn('password_reset_tokens', 'id')) {
                $table->bigIncrements('id')->first();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop id if it exists
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_tokens', 'id')) {
                $table->dropColumn('id');
            }
        });

        // Step 2: Restore email and token as NOT NULL + make email primary
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 191)->change();   // back to NOT NULL
            $table->string('token', 191)->change();   // back to NOT NULL
            $table->primary('email');
        });
    }
};
