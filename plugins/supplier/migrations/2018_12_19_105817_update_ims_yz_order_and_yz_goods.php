<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateImsYzOrderAndYzGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_order')) {
            Schema::table('yz_order', function (Blueprint $table) {
                if (Schema::hasColumn('yz_order', 'plugin_id') && Schema::hasColumn('yz_order', 'is_plugin'))
                {
                    \app\common\models\Order::where('is_plugin',1)->update(['plugin_id'=>92]);
                }
            });
        }
        if (Schema::hasTable('yz_goods')) {
            Schema::table('yz_goods', function (Blueprint $table) {
                if (Schema::hasColumn('yz_goods', 'plugin_id') && Schema::hasColumn('yz_goods', 'is_plugin'))
                {
                    \app\common\models\Goods::where('is_plugin',1)->update(['plugin_id'=>92]);
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
