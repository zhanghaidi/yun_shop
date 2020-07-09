<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImzYzOrderGoodsDiyFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_order_goods_diy_form')) {
            Schema::create('yz_order_goods_diy_form', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('form_id')->nullable();
                $table->integer('diyform_data_id')->nullable();
                $table->integer('order_goods_id')->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
        //
        if (Schema::hasTable('yz_order_goods_diy_form')) {

            Schema::drop('yz_order_goods_diy_form');
        }
    }
}
