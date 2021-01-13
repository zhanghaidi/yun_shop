<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzWxComplaintItemTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_wx_complaint_item')) {
            Schema::create('yz_wx_complaint_item', function (Blueprint $table) {
                $table->increments('id');
				$table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->tinyInteger('type', false, true)->default(0)->comment('类型:1=网页,2=公众号,3=微信群');
                $table->integer('pid', false, true)->default(0)->comment('父ID');
                $table->string('name', 255)->default('')->comment('投诉项内容');
                $table->tinyInteger('submit_mode', false, true)->default(0)->comment('投诉方式:1=直接提交,2=需要文字或图片的材料');
                $table->smallInteger('order', false, true)->default(0)->comment('排序');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_wx_complaint_item');
    }
}
