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
        Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');

        $table->string('phone')->nullable();
        $table->unsignedBigInteger('user_type_id')->default(1); // default: seeker
        $table->unsignedBigInteger('city_id')->nullable();

        $table->text('bio')->nullable();
        $table->rememberToken();
        $table->timestamps();

        // Foreign Keys
        $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
        $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
