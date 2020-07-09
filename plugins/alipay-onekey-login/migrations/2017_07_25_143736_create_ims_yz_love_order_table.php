<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzLoveOrderTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_love_order')) {
            Schema::create('yz_love_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid');
                $table->integer('order_id');
                $table->decimal('point', 10);
                $table->decimal('money', 10);
                $table->integer('created_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
        if (!Schema::hasTable('yz_love_order')) {

            Schema::drop('ims_yz_love_order');
        }
    }

}
