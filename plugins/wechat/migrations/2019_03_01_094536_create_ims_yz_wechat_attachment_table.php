<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzWechatAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (!Schema::hasTable('yz_wechat_attachment')) {
            Schema::create('yz_wechat_attachment', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);  
                $table->integer('acid')->default(0);  
                $table->integer('uid')->default(0);  
                $table->string('filename',255)->default('');  
                $table->string('attachment',255)->default('');  
                $table->string('media_id',255)->default('');
                $table->integer('width')->default(0);  
                $table->integer('height')->default(0);    
                $table->string('type',255)->default('');  
                $table->string('model',255)->default('');  
                $table->string('tag',5000)->default('');
                $table->integer('createtime')->default(0);
                $table->string('module_upload_dir',255)->default('');  
                $table->integer('group_id')->nullable();
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
        Schema::dropIfExists('yz_wechat_attachment');
    }
}
