<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamPaperTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_paper')) {
            Schema::create('yz_exam_paper', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->string('name', 100)->default('')->comment('名称');
                $table->tinyInteger('random_question', false, true)->default(0)->comment('题目乱序');
                $table->tinyInteger('random_answer', false, true)->default(0)->comment('选择乱序');
                $table->string('random_topic', 255)->default('')->comment('随机选题:single,multiple,judgment,blank,qa');
                $table->tinyInteger('question', false, true)->default(0)->comment('题目数量');
                $table->smallInteger('score', false, true)->default(0)->comment('总分值');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_paper');
    }
}
