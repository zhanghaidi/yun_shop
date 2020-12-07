<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzFaceAnalysisLogTable extends Migration
{
	public function up()
	{
		if (!Schema::hasTable('yz_face_analysis_log')) {
			Schema::create('yz_face_analysis_log', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
				$table->integer('member_id', false, true)->default(0)->comment('对应mc_member表id');
				$table->string('url', 200)->default('')->comment('图片URL');
				$table->tinyInteger('gender', false, true)->default(0)->comment('性别:1=女,2=男');
				$table->tinyInteger('age', false, true)->default(0)->comment('年龄');
				$table->tinyInteger('beauty', false, true)->default(0)->comment('魅力');
				$table->tinyInteger('expression', false, true)->default(0)->comment('笑脸:正常0 - 微笑50 - 大笑100');
				$table->tinyInteger('hat', false, true)->default(0)->comment('是否有帽子:0=无,1=有');
				$table->tinyInteger('glass', false, true)->default(0)->comment('是否有眼镜:0=无,1=有');
				$table->tinyInteger('mask', false, true)->default(0)->comment('是否有口罩:0=无,1=有');
				$table->tinyInteger('hair-length', false, true)->default(0)->comment('头发长度:0光头, 1短发, 2中发, 3长发, 4绑发');
				$table->tinyInteger('hair-bang', false, true)->default(0)->comment('是否有刘海:0=无,1=有');
				$table->tinyInteger('hair-color', false, true)->default(0)->comment('发色:0=黑色,1=金色,2=棕色,3=灰白色');
				$table->string('attribute',200)->default('')->comment('人脸属性');
				$table->string('quality',200)->default('')->comment('图片质量');
				$table->smallInteger('cost', false, true)->default(0)->comment('花费');
				$table->smallInteger('gain', false, true)->default(0)->comment('收益');
				$table->mediumInteger('label', false, true)->default(0)->comment('标识');
				$table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
			});
		}
	}

	public function down()
	{
		Schema::drop('yz_face_analysis_log');
	}
}
