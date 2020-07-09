<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPosterSupplementTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_poster_supplement')) {
            Schema::create('yz_poster_supplement', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('poster_id')->unsigned()->unique('uk_poster_id');
                $table->string('not_start_reminder', 140)->nullable()->default('');
                $table->string('finish_reminder', 140)->nullable()->default('');
                $table->string('wait_reminder', 140)->nullable()->default('');
                $table->string('not_open_reminder', 140)->nullable()->default('');
                $table->string('not_open_reminder_url')->nullable()->default('');
                $table->integer('recommender_credit')->unsigned()->nullable()->default(0);
                $table->decimal('recommender_bonus', 14)->unsigned()->nullable()->default(0.00);
                $table->integer('recommender_coupon_id')->unsigned()->nullable()->default(0);
                $table->string('recommender_coupon_name', 15)->nullable()->default('');
                $table->integer('recommender_coupon_num')->unsigned()->nullable()->default(0);
                $table->integer('subscriber_credit')->unsigned()->nullable()->default(0);
                $table->decimal('subscriber_bonus', 14)->unsigned()->nullable()->default(0.00);
                $table->integer('subscriber_coupon_id')->unsigned()->nullable()->default(0);
                $table->string('subscriber_coupon_name', 15)->nullable()->default('');
                $table->integer('subscriber_coupon_num')->unsigned()->nullable()->default(0);
                $table->boolean('bonus_method')->nullable()->default(1);
                $table->string('recommender_award_notice', 140)->nullable()->default('');
                $table->string('subscriber_award_notice', 140)->nullable()->default('');
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
		Schema::dropIfExists('yz_poster_supplement');
	}

}
