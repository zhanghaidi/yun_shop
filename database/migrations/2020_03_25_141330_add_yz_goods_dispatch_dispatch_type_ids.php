<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYzGoodsDispatchDispatchTypeIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_goods_dispatch')) {//因为要从供货平台子平台导入模板，所以这里要添加插件id
            Schema::table('yz_goods_dispatch', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_goods_dispatch', 'dispatch_type_ids')) {
                    $table->string('dispatch_type_ids')->nullable();
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
