<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamExaminationTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_examination')) {
            Schema::create('yz_exam_examination', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->string('name', 100)->default('')->comment('名称');
				$table->string('url', 200)->default('')->comment('封面图片URL');
                $table->timestamp('start')->nullable()->comment('考试开始时间');
                $table->timestamp('end')->nullable()->comment('考试结束时间');
                $table->smallInteger('duration', false, true)->default(0)->comment('考试时长:单位分,0=不限');
                $table->tinyInteger('frequency', false, true)->default(0)->comment('参与次数:0=不限');
                $table->tinyInteger('interval', false, true)->default(0)->comment('重考间隔:单位时,0=不限');
                $table->tinyInteger('is_question_score', false, true)->default(0)->comment('题目分值是否显示');
                $table->tinyInteger('is_score', false, true)->default(0)->comment('结束是否显示成绩');
                $table->tinyInteger('is_question', false, true)->default(0)->comment('题目展示');
                $table->tinyInteger('is_answer', false, true)->default(0)->comment('答案展示');
                $table->tinyInteger('status', false, true)->default(0)->comment('状态:1=开启,2=关闭');
                $table->integer('paper_id', false, true)->default(0)->comment('试卷ID');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_examination');
    }
}
