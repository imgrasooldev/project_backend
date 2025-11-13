<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(){
    Schema::table('job_posts', function (Blueprint $table) {
        $table->boolean('is_active')->default(1)->after('provider_id');
    });
}

public function down(){
    Schema::table('job_posts', function (Blueprint $table) {
        $table->dropColumn('is_active');
    });
}

};
