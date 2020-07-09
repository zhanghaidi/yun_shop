<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPosterAwardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_poster_award')) {
            Schema::create('yz_poster_award', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->unsigned();
                $table->integer('poster_id')->unsigned()->index('poster_id');
                $table->integer('subscriber_memberid')->unsigned()->index('idx_subscriber_memberid');
                $table->integer('recommender_memberid')->unsigned()->index('idx_recommender_memberid');
                $table->integer('recommender_credit')->unsigned()->nullable()->default(0);
                $table->integer('recommender_bonus')->unsigned()->nullable()->default(0);
                $table->integer('recommender_coupon_id')->unsigned()->nullable()->default(0);
                $table->integer('recommender_coupon_num')->unsigned()->nullable()->default(0);
                $table->integer('subscriber_credit')->unsigned()->nullable()->default(0);
                $table->integer('subscriber_bonus')->unsigned()->nullable()->default(0);
                $table->integer('subscriber_coupon_id')->unsigned()->nullable()->default(0);
                $table->integer('subscriber_coupon_num')->unsigned()->nullable()->default(0);
                $table->integer('created_at')->unsigned()->nullable();
                $table->integer('updated_at')->unsigned()->nullable();
                $table->integer('deleted_at')->unsigned()->nullable();
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
		Schema::dropIfExists('yz_poster_award');
	}

}
