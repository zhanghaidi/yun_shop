<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamAnswerPaperContentTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_answer_paper_content')) {
            Schema::create('yz_exam_answer_paper_content', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('answer_paper_id', false, true)->default(0)->comment('答卷ID');
                $table->longText('content')->nullable()->comment('答案:JSON格式，参考模型说明');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_answer_paper_content');
    }
}
