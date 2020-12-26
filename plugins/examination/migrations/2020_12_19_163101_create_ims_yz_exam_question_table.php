<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamQuestionTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_question')) {
            Schema::create('yz_exam_question', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->mediumInteger('sort_id', false, true)->default(0)->comment('分类ID');
                $table->tinyInteger('type', false, true)->default(0)->comment('类型:1=单选,2=多选,3=判断,4=填空,5=问答');
                $table->text('problem')->nullable()->comment('题目');
                $table->integer('log_id', false, true)->default(0)->comment('题目日志ID');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_question');
    }
}
