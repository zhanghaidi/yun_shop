<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzGoodsGroupGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_group_goods')) {
            Schema::create('yz_goods_group_goods', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->string('group_goods_id', 20);
                $table->string('group_id', 20);
                $table->string('goods_id', 20);
                $table->text('goods');
                $table->string('group_type', 30);
                $table->integer('Identification')->default(0);
                $table->string('temp',25);
                $table->integer('created_at')->nullable()->default(0)->index('idx_createtime');
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
        //
    }
}
