<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDesignerMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_designer_menu')) {
            Schema::create('yz_designer_menu', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->string('menu_name')->nullable();
                $table->boolean('is_default')->nullable()->default(0)->index('idx_isdefault');
                $table->integer('created_at')->nullable()->default(0)->index('idx_createtime');
                $table->text('menus', 65535)->nullable();
                $table->text('params', 65535)->nullable();
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
		Schema::dropIfExists('yz_designer_menu');
	}

}
