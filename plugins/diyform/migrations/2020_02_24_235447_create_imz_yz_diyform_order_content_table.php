<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImzYzDiyformOrderContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_diyform_order_content')) {
            Schema::create('yz_diyform_order_content', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->integer('form_id')->nullable();
                $table->integer('goods_id')->nullable();
                $table->integer('order_id')->nullable();
                $table->text('data', 65535)->nullable();
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
        //
        if (Schema::hasTable('yz_diyform_order_content')) {

            Schema::drop('yz_diyform_order_content');
        }
    }
}
