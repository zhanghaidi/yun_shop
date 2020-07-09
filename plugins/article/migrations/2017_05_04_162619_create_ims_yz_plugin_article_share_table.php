<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPluginArticleShareTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_plugin_article_share')) {
            Schema::create('yz_plugin_article_share', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->comment('公众号id');
                $table->integer('article_id')->nullable()->comment('文章id');
                $table->integer('share_uid')->nullable()->comment('分享者的uid');
                $table->integer('click_uid')->nullable()->comment('阅读者的uid');
                $table->integer('click_time')->nullable()->comment('点击时间');
                $table->integer('point')->nullable()->comment('奖励的积分');
                $table->integer('credit')->nullable()->comment('奖励的余额');

                $table->index('uniacid');
                $table->index('article_id');
                $table->index('share_uid');
                $table->index('click_uid');
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
		Schema::dropIfExists('yz_plugin_article_share');
	}

}
