<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerificationMailLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_mail_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip')->default('');
            $table->string('device_id')->default('');
            $table->string('browser')->default('');
            $table->string('os')->default('');
            $table->string('email')->default('');
            $table->string('verification_code')->default('');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verification_mail_log');
    }
}
