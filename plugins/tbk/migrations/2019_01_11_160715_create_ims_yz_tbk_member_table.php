<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTbkMemberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_tbk_member')) {
            Schema::create('yz_tbk_member', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->string('pid', 20)->nullable()->unique('idx_tb_id')->comment('订单号后6位做关联');
                $table->string('full_pid', 30)->nullable();
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
		Schema::drop('ims_yz_tbk_member');
	}

}
