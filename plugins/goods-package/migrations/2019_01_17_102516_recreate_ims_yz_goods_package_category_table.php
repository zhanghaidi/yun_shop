<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateImsYzGoodsPackageCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_package_category')) {
            Schema::create('yz_goods_package_category', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('category_package_id');
                $table->integer('category_sort');
                $table->string('category_name');
                $table->string('category_goods_ids');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('yz_goods_package_category');
    }
}
