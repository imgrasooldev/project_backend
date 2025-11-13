<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('job_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('job_posts', 'deleted_at')) {
                $table->softDeletes(); // adds deleted_at column
            }
        });
    }

    public function down()
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropSoftDeletes(); // drops deleted_at column
        });
    }
};
