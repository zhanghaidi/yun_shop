<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzJdSupplyGoodsOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('yz_jd_supply_goods_option')) {
            Schema::create('yz_jd_supply_goods_option', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('goods_id')->default(0)->nullable()->index('goods_idx')->comment('商品ID');
                $table->integer('option_id')->default(0)->nullable()->index('goods_option_idx')->comment('商城商品规格id');
                $table->integer('jd_goods_id')->default(0)->nullable()->index('jd_goods_idx')->comment('第三方商品ID');
                $table->integer('jd_option_id')->default(0)->nullable()->comment('第三方商品规格id');
                $table->text('shop_goods_specs')->nullable()->comment('商城规格项ids');
                $table->text('spec_value_ids')->nullable()->comment('第三方规格值ids');
                $table->text('spec_value_names')->nullable()->comment('第三方规格名称');
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
        Schema::dropIfExists('yz_jd_supply_goods_option');
    }
}
