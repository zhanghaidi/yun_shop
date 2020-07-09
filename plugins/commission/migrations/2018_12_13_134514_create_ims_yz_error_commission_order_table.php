<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateImsYzErrorCommissionOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_error_commission_order')) {
            Schema::create('yz_error_commission_order', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('commission_order_id')->nullable();
                $table->integer('member_id')->nullable();
                $table->string('order_id', 23)->default('');
                $table->text('note')->nullable();
                $table->decimal('commission_amount', 12,2)->nullable()->default(0.00);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted')->nullable();
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
		Schema::dropIfExists('yz_error_commission_order');
	}

}
