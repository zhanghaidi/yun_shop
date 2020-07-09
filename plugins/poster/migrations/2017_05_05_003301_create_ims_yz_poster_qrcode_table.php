<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPosterQrcodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_poster_qrcode')) {
            Schema::create('yz_poster_qrcode', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->unsigned();
                $table->integer('poster_id')->unsigned()->index('idx_posterid');
                $table->integer('qrcode_id')->unsigned()->index('idx_qrcodeid');
                $table->integer('memberid')->unsigned();
                $table->integer('created_at')->unsigned()->nullable();
                $table->integer('updated_at')->unsigned()->nullable();
                $table->integer('deleted_at')->unsigned()->nullable();
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
		Schema::dropIfExists('yz_poster_qrcode');
	}

}
