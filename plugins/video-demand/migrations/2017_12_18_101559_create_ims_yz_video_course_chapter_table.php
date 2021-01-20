<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzVideoCourseChapterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_video_course_chapter')) {
            Schema::create('yz_video_course_chapter', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('course_id')->nullable()->comment('课程ID');
                $table->string('chapter_name')->nullable()->comment('章节名称');
                $table->text('video_address', 65535)->nullable()->comment('视频地址');
                $table->boolean('is_audition')->nullable()->comment('是否试听');
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
		Schema::drop('ims_yz_video_course_chapter');
	}

}
