<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDesignerTopMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_designer_top_menu')) {
            Schema::create('yz_designer_top_menu', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->string('menu_name', 45)->nullable();
                $table->text('menus', 65535)->nullable();
                $table->text('params', 65535)->nullable();
                $table->integer('created_at')->nullable()->index('idx_createtime');
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
		Schema::dropIfExists('ims_yz_designer_top_menu');
	}

}
