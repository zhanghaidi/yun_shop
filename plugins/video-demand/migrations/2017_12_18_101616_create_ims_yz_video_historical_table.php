<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzVideoHistoricalTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_video_historical')) {
            Schema::create('yz_video_historical', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->integer('course_id')->nullable()->comment('课程ID');
                $table->integer('course_chapter_id')->nullable()->comment('章节ID');
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
		Schema::drop('ims_yz_video_historical');
	}

}
