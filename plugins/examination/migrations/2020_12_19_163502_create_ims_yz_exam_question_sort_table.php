<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamQuestionSortTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_question_sort')) {
            Schema::create('yz_exam_question_sort', function (Blueprint $table) {
                $table->mediumIncrements('id');
                $table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->mediumInteger('pid', false, true)->default(0)->comment('父类ID');
                $table->string('name', 100)->default('')->comment('名称');
                $table->smallInteger('order', false, true)->default(0)->comment('排序');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_question_sort');
    }
}
