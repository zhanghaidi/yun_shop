<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYzGoodsGroupGoodsPageId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_group_goods')) {
            Schema::table('yz_goods_group_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_goods_group_goods', 'page_id')) {
                    $table->integer('page_id');
                }
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
