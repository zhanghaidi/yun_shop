<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPosterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_poster')) {
            Schema::create('yz_poster', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->unsigned()->index('idx_uniacid');
                $table->string('title', 50)->index('idx_title');
                $table->boolean('type');
                $table->string('keyword', 30);
                $table->integer('time_start')->unsigned()->nullable()->default(0);
                $table->integer('time_end')->unsigned()->nullable()->default(0);
                $table->string('background')->nullable()->default('');
                $table->text('style_data', 65535);
                $table->string('response_title', 50)->nullable()->default('');
                $table->string('response_thumb')->nullable()->default('');
                $table->string('response_desc')->nullable()->default('');
                $table->string('response_url')->nullable()->default('');
                $table->boolean('is_open')->nullable()->default(0);
                $table->boolean('auto_sub')->nullable()->default(1);
                $table->boolean('status')->nullable()->default(1);
                $table->boolean('center_show')->nullable()->default(0);
                $table->integer('created_at')->unsigned()->nullable();
                $table->integer('updated_at')->unsigned()->nullable();
                $table->integer('deleted_at')->unsigned()->nullable();
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
		Schema::dropIfExists('yz_poster');
	}

}
