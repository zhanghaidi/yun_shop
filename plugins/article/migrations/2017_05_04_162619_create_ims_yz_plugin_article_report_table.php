<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPluginArticleReportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_plugin_article_report')) {
            Schema::create('yz_plugin_article_report', function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('uniacid')->nullable();
                $table->integer('uid')->nullable();
                $table->integer('article_id')->nullable();
                $table->string('type')->nullable();
                $table->string('desc')->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();

                $table->index('uniacid');
                $table->index('article_id');
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
		Schema::dropIfExists('yz_plugin_article_report');
	}

}
