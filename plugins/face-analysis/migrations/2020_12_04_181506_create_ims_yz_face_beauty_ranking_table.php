<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzFaceBeautyRankingTable extends Migration
{
	public function up()
	{
		if (!Schema::hasTable('yz_face_beauty_ranking')) {
			Schema::create('yz_face_beauty_ranking', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
				$table->mediumInteger('label', false, true)->default(0)->comment('标识');
				$table->tinyInteger('type', false, true)->default(0)->comment('榜单类型');
				$table->integer('member_id', false, true)->default(0)->comment('对应mc_member表id');
				$table->tinyInteger('gender', false, true)->default(0)->comment('性别:1=女,2=男');
				$table->tinyInteger('age', false, true)->default(0)->comment('年龄');
				$table->tinyInteger('beauty', false, true)->default(0)->comment('魅力');
				$table->tinyInteger('status', false, true)->default(0)->comment('状态:1=有效,2=无效');
				$table->integer('like', false, true)->default(0)->comment('点赞量');
				$table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
			});
		}
	}

	public function down()
	{
		Schema::drop('yz_face_beauty_ranking');
	}
}
