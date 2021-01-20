<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzNominateBonusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_nominate_bonus')) {
            Schema::create('yz_nominate_bonus',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')
                        ->nullable();
                    $table->integer('uid')
                        ->nullable();
                    $table->integer('level_id')
                        ->nullable();
                    $table->integer('source_id')
                        ->nullable();
                    $table->decimal('amount',
                        10)
                        ->nullable();
                    $table->boolean('status')
                        ->nullable();
                    $table->boolean('type')
                        ->nullable()->comment('0直推奖1直推极差奖2团队奖');
                    $table->integer('created_at')
                        ->nullable();
                    $table->integer('updated_at')
                        ->nullable();
                    $table->integer('deleted_at')
                        ->nullable();
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
		Schema::drop('yz_nominate_bonus');
	}

}
