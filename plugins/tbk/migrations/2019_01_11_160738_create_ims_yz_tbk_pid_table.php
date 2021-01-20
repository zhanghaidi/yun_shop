<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTbkPidTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_tbk_pid')) {
            Schema::create('yz_tbk_pid', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->string('name', 100)->nullable();
                $table->string('full_pid', 50)->nullable();
                $table->string('pid', 20)->nullable();
                $table->integer('is_user')->nullable()->default(0)->comment('是否使用，使用为1');
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
		Schema::drop('ims_yz_tbk_pid');
	}

}
