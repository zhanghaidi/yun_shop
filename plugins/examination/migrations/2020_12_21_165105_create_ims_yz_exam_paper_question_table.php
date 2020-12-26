<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamPaperQuestionTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_paper_question')) {
            Schema::create('yz_exam_paper_question', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('paper_id', false, true)->default(0)->comment('试卷ID');
                $table->integer('question_id', false, true)->default(0)->comment('问题ID');
                $table->tinyInteger('type', false, true)->default(0)->comment('问题类型');
                $table->string('problem', 50)->default('')->comment('问题题目');
                $table->tinyInteger('score', false, true)->default(0)->comment('分值');
                $table->string('option', 50)->default('')->comment('分值选项');
                $table->tinyInteger('order', false, true)->default(0)->comment('排序');
                $table->integer('created_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_paper_question');
    }
}
