<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('service_profiles', function (Blueprint $table) {
        $table->boolean('is_active')->default(1)->after('available_time');
    });
}

public function down()
{
    Schema::table('service_profiles', function (Blueprint $table) {
        $table->dropColumn('is_active');
    });
}

};
