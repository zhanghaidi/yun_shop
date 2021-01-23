<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzWxComplaintProjectTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_wx_complaint_project')) {
            Schema::create('yz_wx_complaint_project', function (Blueprint $table) {
                $table->increments('id');
				$table->integer('uniacid', false, true)->default(0)->comment('公众号ID');
                $table->string('name', 100)->default('')->comment('项目名称');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_wx_complaint_project');
    }
}
