<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzWxComplaintLogTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_wx_complaint_log')) {
            Schema::create('yz_wx_complaint_log', function (Blueprint $table) {
                $table->increments('id');
				$table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->integer('member_id', false, true)->default(0)->comment('对应mc_member表id');
                $table->integer('project_id', false, true)->default(0)->comment('投诉项目ID');
                $table->integer('item_id', false, true)->default(0)->comment('投诉项内容ID');
                $table->integer('created_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_wx_complaint_log');
    }
}
