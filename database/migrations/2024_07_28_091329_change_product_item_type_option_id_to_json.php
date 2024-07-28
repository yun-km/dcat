<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductItemTypeOptionIdToJson extends Migration
{
    public function up()
    {
        Schema::table('product_option_inventories', function (Blueprint $table) {
            $table->dropColumn('product_item_type_option_id');
        });

        Schema::table('product_option_inventories', function (Blueprint $table) {
            $table->json('product_item_type_option_id')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('product_option_inventories', function (Blueprint $table) {
            $table->dropColumn('product_item_type_option_id');
        });

        Schema::table('product_option_inventories', function (Blueprint $table) {
            $table->string('product_item_type_option_id', 191)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(false)->after('id');
        });
    }
}
