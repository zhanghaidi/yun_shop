<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzWechatNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_wechat_news')) {
            Schema::create('yz_wechat_news', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('attach_id')->default(0);
                $table->string('thumb_media_id')->default('');
                $table->string('thumb_url')->default('');
                $table->string('title')->default('');
                $table->string('author')->default('');
                $table->string('digest')->default('');
                $table->text('content')->nullable();
                $table->string('content_source_url')->default('');
                $table->integer('show_cover_pic')->default(0);
                $table->string('url')->default('');
                $table->integer('displayorder')->default(0);
                $table->integer('need_open_comment')->default(0);
                $table->integer('only_fans_can_comment')->default(0);
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
        Schema::dropIfExists('yz_wechat_news');
    }
}
