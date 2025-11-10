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
        Schema::table('notifications', function (Blueprint $table) {
            // Remove old column
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            // Add sender and receiver IDs
            $table->unsignedBigInteger('sender_id')->after('id')->index();
            $table->unsignedBigInteger('receiver_id')->after('sender_id')->index();

            // Add foreign keys
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');

            // Application or related record reference
            $table->unsignedBigInteger('application_id')->nullable()->after('receiver_id')->index();

            // Status ENUM
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending')->after('application_id');

            // Read tracking for sender & receiver
            $table->boolean('read_by_sender')->default(false)->after('status');
            $table->timestamp('read_by_sender_at')->nullable()->after('read_by_sender');

            $table->boolean('read_by_receiver')->default(false)->after('read_by_sender_at');
            $table->timestamp('read_by_receiver_at')->nullable()->after('read_by_receiver');

            // Optional: additional tracking columns
            $table->timestamp('sent_at')->nullable()->change();
            $table->timestamp('read_at')->nullable()->change(); // old field — optional to keep for compatibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);

            $table->dropColumn([
                'sender_id',
                'receiver_id',
                'application_id',
                'status',
                'read_by_sender',
                'read_by_sender_at',
                'read_by_receiver',
                'read_by_receiver_at',
            ]);

            // Re-add old user_id column for rollback
            $table->unsignedBigInteger('user_id')->index()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
