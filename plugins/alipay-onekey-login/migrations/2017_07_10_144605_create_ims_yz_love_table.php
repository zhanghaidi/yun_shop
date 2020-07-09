<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzLoveTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_love')) {
            Schema::create('yz_love', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->decimal('old_value', 14)->nullable();
                $table->decimal('change_value', 14);
                $table->decimal('new_value', 14);
                $table->boolean('value_type');
                $table->boolean('type');
                $table->boolean('source');
                $table->string('relation', 45);
                $table->string('operator',45);
                $table->string('operator_id', 45);
                $table->string('remark', 200)->default('');
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
		Schema::drop('ims_yz_love');
	}

}
