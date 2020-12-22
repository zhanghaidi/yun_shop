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
                $table->smallInteger('score', false, true)->default(0)->comment('得分');
				$table->text('answer')->nullable()->comment('答案:JSON格式，参考模型说明');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_answer_paper');
    }
}
