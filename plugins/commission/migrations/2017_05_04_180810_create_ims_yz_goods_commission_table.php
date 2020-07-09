<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzGoodsCommissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_commission')) {
            Schema::create('yz_goods_commission', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('goods_id')->index('idx_good_id');
                $table->integer('is_commission')->nullable();
                $table->boolean('show_commission_button')->default(0);
                $table->string('poster_picture')->nullable();
                $table->boolean('has_commission')->nullable()->default(0);
                $table->integer('first_level_rate')->nullable();
                $table->decimal('first_level_pay', 14)->nullable();
                $table->integer('second_level_rate')->nullable();
                $table->decimal('second_level_pay', 14)->nullable();
                $table->integer('third_level_rate')->nullable();
                $table->decimal('third_level_pay', 14)->nullable();
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
		Schema::dropIfExists('yz_goods_commission');
	}

}
