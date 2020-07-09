<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPluginArticleLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    if(!Schema::hasTable('yz_plugin_article_log'))
	    {
            Schema::create('yz_plugin_article_log', function(Blueprint $table)
            {
                $table->increments('id');
                $table->tinyInteger('uniacid')->nullable()->comment('公众号ID');
                $table->integer('article_id')->nullable()->comment('文章ID');
                $table->integer('uid')->nullable()->comment('用户ID');
                $table->integer('read_num')->nullable()->comment('阅读数');
                $table->boolean('liked')->nullable()->comment('是否点赞');

                $table->index('uniacid');
                $table->index('article_id');
                $table->index('uid');
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
        Schema::dropIfExists('yz_plugin_article_log');
	}

}
