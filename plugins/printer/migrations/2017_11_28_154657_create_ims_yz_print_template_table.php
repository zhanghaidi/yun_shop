<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPrintTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_print_template')) {
            Schema::create('yz_print_template', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->string('title')->default('');
                $table->string('print_title')->default('');
                $table->string('print_style')->default('');
                $table->text('print_data', 65535);
                $table->string('qr_code')->nullable()->default('');
                $table->integer('owner');
                $table->integer('owner_id')->nullable()->default(0);
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
		Schema::drop('ims_yz_print_template');
	}

}
