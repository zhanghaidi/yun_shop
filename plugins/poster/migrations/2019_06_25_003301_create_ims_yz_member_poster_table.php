<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMemberPosterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_member_poster')) {
            Schema::create('yz_member_poster', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->unsigned()->unique('uk_uid');
                $table->tinyInteger('status')->default(0);// 0未生成,1生成中,2已生成,-1生成失败
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
		Schema::dropIfExists('yz_member_poster');
	}

}
