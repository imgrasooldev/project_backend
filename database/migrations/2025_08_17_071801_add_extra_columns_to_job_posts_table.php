<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->enum('type', ['direct', 'posted'])->default('posted')->after('description');
            $table->enum('status', ['open', 'assigned', 'completed', 'cancelled'])->default('open')->after('type');
            $table->decimal('budget', 10, 2)->nullable()->after('status');
            $table->decimal('location_lat', 10, 7)->nullable()->after('budget');
            $table->decimal('location_lng', 10, 7)->nullable()->after('location_lat');
            $table->string('address')->nullable()->after('location_lng');
            $table->date('expiry_date')->nullable()->after('desired_time');
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'status',
                'budget',
                'location_lat',
                'location_lng',
                'address',
                'expiry_date',
            ]);
        });
    }
};
