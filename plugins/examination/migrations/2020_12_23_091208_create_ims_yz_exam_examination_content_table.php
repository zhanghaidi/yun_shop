<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzExamExaminationContentTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_exam_examination_content')) {
            Schema::create('yz_exam_examination_content', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('examination_id', false, true)->default(0)->comment('考试ID');
                $table->string('share_title', 200)->default('')->comment('分享标题');
				$table->string('share_image', 200)->default('')->comment('分享图片URL');
                $table->text('content')->nullable()->comment('内容');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_exam_examination_content');
    }
}
