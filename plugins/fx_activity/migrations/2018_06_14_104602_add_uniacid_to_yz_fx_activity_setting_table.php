<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniacidToYzFxActivitySettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_fx_activity_setting')) {
            Schema::table('yz_fx_activity_setting', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_fx_activity_setting', 'uniacid')) {
                    $table->integer('uniacid')->default(0);
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
        if (Schema::hasTable('yz_fx_activity_setting')) {
            Schema::table('yz_fx_activity_setting', function (Blueprint $table) {
                $table->dropColumn('uniacid');
            });
        }
    }
}
