<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzWechatUserapiReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (!Schema::hasTable('yz_wechat_userapi_reply')) {
            Schema::create('yz_wechat_userapi_reply', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('rid')->default(0);  
                $table->string('description',255)->default('');  
                $table->string('apiurl',300)->default('');  
                $table->string('token',32)->default('');  
                $table->string('default_text',100)->default('');
                $table->integer('cachetime')->default(0);
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
        Schema::dropIfExists('yz_wechat_userapi_reply');
    }
}
