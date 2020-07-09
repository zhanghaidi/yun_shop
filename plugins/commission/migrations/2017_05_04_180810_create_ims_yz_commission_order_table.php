<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzCommissionOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_commission_order')) {
            Schema::create('yz_commission_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->string('type', 60)->nullable();
                $table->integer('type_id')->nullable();
                $table->integer('buy_id')->nullable();
                $table->integer('member_id')->default(0);
                $table->decimal('commission_amount', 14)->nullable()->default(0.00);
                $table->string('formula', 60)->nullable();
                $table->integer('hierarchy')->nullable()->default(1);
                $table->integer('commission_rate')->nullable()->default(0);
                $table->decimal('commission', 14)->nullable()->default(0.00);
                $table->boolean('status')->nullable()->default(0);
                $table->integer('recrive_at')->nullable();
                $table->integer('settle_days')->nullable()->default(0);
                $table->integer('statement_at')->nullable();
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
		Schema::dropIfExists('yz_commission_order');
	}

}
