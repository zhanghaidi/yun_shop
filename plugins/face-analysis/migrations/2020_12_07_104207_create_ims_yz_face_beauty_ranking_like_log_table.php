<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzFaceBeautyRankingLikeLogTable extends Migration
{
	public function up()
	{
		if (!Schema::hasTable('yz_face_beauty_ranking_like_log')) {
			Schema::create('yz_face_beauty_ranking_like_log', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('ranking_id', false, true)->default(0)->comment('排行榜ID');
				$table->integer('member_id', false, true)->default(0)->comment('对应mc_member表id');
				$table->integer('created_at')->nullable();
			});
		}
	}

	public function down()
	{
		Schema::drop('yz_face_beauty_ranking_like_log');
	}
}
