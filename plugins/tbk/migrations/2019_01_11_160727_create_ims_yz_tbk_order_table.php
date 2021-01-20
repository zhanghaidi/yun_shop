<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTbkOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_tbk_order')) {
            Schema::create('yz_tbk_order', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->string('order_sn', 23)->nullable()->default('');
                $table->string('adzone_id', 20)->nullable()->comment('广告位ID');
                $table->string('adzone_name', 100)->nullable()->comment('广告位名称');
                $table->decimal('alipay_total_price', 10, 4)->nullable()->comment('付款金额');
                $table->string('auction_category', 100)->nullable()->comment('类目名称');
                $table->decimal('commission', 10)->nullable();
                $table->decimal('commission_rate', 8, 4)->nullable();
                $table->dateTime('create_time')->nullable();
                $table->dateTime('earning_time')->nullable()->comment('结算时间');
                $table->decimal('income_rate', 8, 4)->nullable()->comment('收入比率');
                $table->integer('item_num')->nullable();
                $table->string('item_title', 300)->nullable();
                $table->string('num_iid', 30)->nullable();
                $table->string('order_type', 50)->nullable()->comment('订单类型');
                $table->string('tk_status', 50)->nullable()->comment('淘客订单状态，3：订单结算，12：订单付款， 13：订单失效，14：订单成功
');
                $table->decimal('pay_price', 10)->nullable()->comment('结算金额');
                $table->decimal('price', 10)->nullable()->comment('单价');
                $table->string('site_id', 30)->nullable()->comment('来源媒体ID');
                $table->decimal('pub_share_pre_fee', 10)->nullable()->comment('效果预估，付款金额*(佣金比率+补贴比率)*分成比率
');
                $table->string('seller_nick', 50)->nullable();
                $table->string('seller_shop_title', 50)->nullable();
                $table->string('yz_order_sn', 30)->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('delete_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->decimal('total_commission_rate', 8, 4)->nullable()->comment('佣金比率');
                $table->decimal('total_commission_fee')->nullable()->comment('佣金金额');
                $table->decimal('tech_fee')->nullable()->comment('技术服务费');
                $table->decimal('subsidy_rate', 8, 3)->nullable()->comment('补贴比率');
                $table->decimal('subsidy_fee')->nullable()->comment('补贴金额');
                $table->integer('subsidy_type')->nullable()->comment('补贴类型，天猫:1，聚划算:2，航旅:3，阿里云:4');
                $table->integer('yz_order_status')->nullable()->default(0)->comment('0 商城订单未完成，1已完成');
                $table->integer('is_queue')->nullable()->default(0)->comment('0不在队列，1在队列');
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
		Schema::drop('ims_yz_tbk_order');
	}

}
