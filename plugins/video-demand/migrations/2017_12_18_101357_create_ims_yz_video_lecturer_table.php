<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzVideoLecturerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_video_lecturer')) {
            Schema::create('yz_video_lecturer', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->string('real_name', 11)->nullable()->comment('真实姓名');
                $table->string('mobile', 11)->nullable()->comment('手机');
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
		Schema::drop('ims_yz_video_lecturer');
	}

}
