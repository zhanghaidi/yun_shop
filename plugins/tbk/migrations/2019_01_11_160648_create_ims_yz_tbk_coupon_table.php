<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTbkCouponTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_tbk_coupon')) {
            Schema::create('yz_tbk_coupon', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('goods_id')->nullable();
                $table->decimal('commission_rate', 6)->nullable();
                $table->integer('category')->nullable();
                $table->string('coupon_click_url', 500)->nullable();
                $table->dateTime('coupon_end_time')->nullable();
                $table->string('coupon_info', 200)->nullable();
                $table->integer('coupon_remain_count')->nullable();
                $table->dateTime('coupon_start_time')->nullable();
                $table->integer('coupon_total_count')->nullable();
                $table->string('item_url', 500)->nullable();
                $table->string('nick', 200)->nullable();
                $table->string('num_iid', 20)->nullable();
                $table->string('pict_url', 300)->nullable();
                $table->integer('seller_id')->nullable();
                $table->string('shop_title', 300)->nullable();
                $table->string('small_images', 800)->nullable();
                $table->string('title', 300)->nullable();
                $table->integer('user_type')->nullable();
                $table->integer('volume')->nullable();
                $table->decimal('zk_final_price')->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->decimal('coupon_amount', 10)->nullable();
                $table->decimal('coupon_price', 10)->nullable();
                $table->integer('coupon_status')->nullable();
                $table->integer('status')->nullable();
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
		Schema::drop('ims_yz_tbk_coupon');
	}

}
