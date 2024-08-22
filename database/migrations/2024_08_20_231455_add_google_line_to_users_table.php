<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleLineToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_account', 30)
            ->nullable()
            ->after('password');
            $table->index(['google_account'], 'user_g_idx');

            $table->string('line_id', 64)
                ->nullable()
                ->after('google_account');
            $table->index(['line_id'], 'user_l_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_account');
            $table->dropColumn('line_id');
        });
    }
}
