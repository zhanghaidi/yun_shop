<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYzDispatchPluginId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_dispatch')) {//因为要从供货平台子平台导入模板，所以这里要添加插件id
            Schema::table('yz_dispatch', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_dispatch', 'plugin_id')) {
                    $table->integer('plugin_id')->default(0);
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
