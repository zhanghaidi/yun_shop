<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzExamQuestionLogTable extends Migration
{
	public function up()
	{
		if (!Schema::hasTable('yz_exam_question_log')) {
			Schema::create('yz_exam_question_log', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
				$table->integer('question_id', false, true)->default(0)->comment('题目ID');
				$table->text('problem')->nullable()->comment('题目');
				$table->mediumText('answer')->nullable()->comment('答案:JSON格式，参考模型说明');
				$table->integer('created_at')->nullable();
			});
		}
	}

	public function down()
	{
		Schema::drop('yz_exam_question_log');
	}
}
