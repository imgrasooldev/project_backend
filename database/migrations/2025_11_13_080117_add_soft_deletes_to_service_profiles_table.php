<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('service_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('service_profiles', 'deleted_at')) {
                $table->softDeletes();  // adds deleted_at column
            }
        });
    }

    public function down()
    {
        Schema::table('service_profiles', function (Blueprint $table) {
            $table->dropSoftDeletes(); // drops deleted_at column
        });
    }
};
