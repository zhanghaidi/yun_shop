<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDesignerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_designer')) {
            Schema::create('yz_designer', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->default(0)->index('idx_uniacid');
                $table->string('page_name')->default('');
                $table->boolean('page_type')->default(0)->index('idx_pagetype');
                $table->text('page_info', 65535);
                $table->string('keyword')->nullable()->default('');
                $table->boolean('is_default')->default(0);
                $table->longText('datas', 65535);
                $table->integer('created_at');
                $table->integer('updated_at');
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
		Schema::dropIfExists('yz_designer');
	}

}
