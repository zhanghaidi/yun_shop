<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzLoveActivationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_love_activation')) {
            Schema::create('yz_love_activation', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->decimal('first_order_money', 14)->nullable();
                $table->integer('first_proportion');
                $table->decimal('first_activation_love', 14);
                $table->decimal('second_three_order_money', 14);
                $table->integer('second_three_proportion');
                $table->decimal('last_upgrade_team_leve_award', 14);
                $table->integer('second_three_fetter_proportion');
                $table->decimal('second_three_activation_love', 14)->nullable();
                $table->decimal('sum_activation_love', 14);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->string('order_sn', 45)->nullable()->default('');
                $table->decimal('actual_activation_love', 14)->nullable();
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
		Schema::drop('ims_yz_love_activation');
	}

}
