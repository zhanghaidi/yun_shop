<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSignTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_sign')) {
            Schema::create('yz_sign', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable()->unique('member_id_UNIQUE');
                $table->decimal('cumulative_point', 11)->nullable()->default(0.00);
                $table->integer('cumulative_coupon')->nullable()->default(0);
                $table->integer('cumulative_number')->nullable()->default(0);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
		Schema::drop('ims_yz_sign');
	}

}
