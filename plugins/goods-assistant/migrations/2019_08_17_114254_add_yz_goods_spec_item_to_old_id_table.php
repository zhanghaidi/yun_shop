<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYzGoodsSpecItemToOldIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_spec_item')) {
            if (!Schema::hasColumn('yz_goods_spec_item', 'old_id')) {
                Schema::table('yz_goods_spec_item', function (Blueprint $table) {
                    $table->integer('old_id')->nullable();
                });
            }
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
