<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPosterScanTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_poster_scan')) {
            Schema::create('yz_poster_scan', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->unsigned();
                $table->integer('poster_id')->unsigned()->index('idx_posterid');
                $table->integer('subscriber_memberid')->unsigned()->index('idx_subscriber_memberid');
                $table->integer('recommender_memberid')->unsigned()->index('idx_recommender_memberid');
                $table->boolean('event_type');
                $table->boolean('sign_up_this_time');
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
		Schema::dropIfExists('yz_poster_scan');
	}

}
