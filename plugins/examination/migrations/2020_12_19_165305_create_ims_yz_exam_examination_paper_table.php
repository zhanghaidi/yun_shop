<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamExaminationPaperTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_examination_paper')) {
            Schema::create('yz_exam_examination_paper', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->integer('examination_id', false, true)->default(0)->comment('考试ID');
                $table->integer('question_id', false, true)->default(0)->comment('问题ID');
                $table->tinyInteger('score', false, true)->default(0)->comment('分值');
                $table->smallInteger('order', false, true)->default(0)->comment('排序');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_examination_paper');
    }
}
