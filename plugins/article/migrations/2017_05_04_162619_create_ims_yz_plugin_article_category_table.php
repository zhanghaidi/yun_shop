<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPluginArticleCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_plugin_article_category')) {
            Schema::create('yz_plugin_article_category', function(Blueprint $table)
            {
                $table->increments('id');
                $table->tinyInteger('uniacid')->nullable();
                $table->string('name', 50)->nullable()->comment('类别的名称');
                $table->tinyInteger('member_level_id_limit')->nullable()->comment('允许阅读的会员等级在yz_member_level中的ID值');

                $table->index('uniacid');
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
		Schema::dropIfExists('yz_plugin_article_category');
	}

}
