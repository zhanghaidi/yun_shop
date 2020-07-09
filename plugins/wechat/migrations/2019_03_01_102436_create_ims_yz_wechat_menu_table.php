<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzWechatMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_wechat_menu')) {
            Schema::create('yz_wechat_menu', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('menuid')->default(0);
                $table->integer('type')->default(0);
                $table->string('title')->default(0);
                $table->integer('sex')->default(0);
                $table->integer('group_id')->default(0);
                $table->integer('client_platform_type')->default(0);
                $table->string('area')->default('');
                $table->text('data')->nullable();
                $table->integer('status')->default(0);
                $table->integer('createtime')->default(0);
                $table->integer('isdeleted')->default(0);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_wechat_menu');
    }
}
