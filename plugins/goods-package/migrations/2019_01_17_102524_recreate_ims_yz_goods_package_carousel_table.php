<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateImsYzGoodsPackageCarouselTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_package_carousel')) {
            Schema::create('yz_goods_package_carousel', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('carousel_package_id');
                $table->integer('carousel_sort');
                $table->string('carousel_title')->default('');
                $table->string('carousel_thumb')->default('');
                $table->string('carousel_link')->default('');
                $table->integer('carousel_open_status')->default(0);
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
        Schema::drop('yz_goods_package_carousel');
    }
}
