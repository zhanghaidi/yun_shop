<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzAgentLevelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_agent_level')) {
            Schema::create('yz_agent_level', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->string('name', 100)->default('');
                $table->integer('level')->default(0);
                $table->integer('first_level')->nullable()->default(0);
                $table->integer('second_level')->nullable()->default(0);
                $table->integer('third_level')->nullable()->default(0);
                $table->text('upgraded', 65535)->nullable();
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
		Schema::dropIfExists('yz_agent_level');
	}

}
