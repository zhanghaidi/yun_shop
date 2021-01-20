<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMrytLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_mryt_log')) {
            Schema::create('yz_mryt_log',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->nullable();
                    $table->integer('uid')->nullable();
                    $table->boolean('type')->nullable();
                    $table->text('remark', 65535)->nullable();
                    $table->integer('source_id')->nullable();
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
		Schema::drop('yz_mryt_log');
	}

}
