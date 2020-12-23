<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamAnswerPaperTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_answer_paper')) {
            Schema::create('yz_exam_answer_paper', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->integer('examination_id', false, true)->default(0)->comment('考试ID');
                $table->integer('member_id', false, true)->default(0)->comment('对应mc_member表id');
                $table->smallInteger('score_total', false, true)->default(0)->comment('试卷总分');
                $table->smallInteger('score_obtain', false, true)->default(0)->comment('得分');
                $table->tinyInteger('question_total', false, true)->default(0)->comment('总题数');
                $table->tinyInteger('question_correct', false, true)->default(0)->comment('正确数目');
                $table->tinyInteger('status', false, true)->default(0)->comment('状态:1=进行中,2=结束');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_answer_paper');
    }
}
