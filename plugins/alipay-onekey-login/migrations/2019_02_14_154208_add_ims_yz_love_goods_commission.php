<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzLoveGoodsCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_love_goods')) {
            Schema::table('yz_love_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_love_goods', 'commission')) {
                    $table->text('commission')->nullable();
                }
                if (!Schema::hasColumn('yz_love_goods', 'commission_level_give')) {
                    $table->tinyInteger('commission_level_give')->default(0);
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
