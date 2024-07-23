<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('title')->default('');
            $table->string('summary')->default('');
            $table->string('description')->default('');
            $table->string('cover')->default('');
            $table->json('pictures')->nullable();
            $table->integer('product_category_id');
            $table->string('tags')->default('');
            $table->tinyInteger('is_active')->nullable()->default('0');
            $table->string('slug')->nullable()->default('');
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
        Schema::dropIfExists('products');
    }
}
