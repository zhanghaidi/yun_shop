<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/10
 * Time: 14:24
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToImsYzExhelperPanelTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('yz_exhelper_panel')) {
            Schema::table('yz_exhelper_panel',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_exhelper_panel', 'begin_time')) {
                        $table->string('begin_time', 100)->nullable();
                    }
                    if (!Schema::hasColumn('yz_exhelper_panel', 'end_time')) {
                        $table->string('end_time', 100)->nullable();
                    }
                });
        }
    }

    public function down()
    {
        Schema::table('yz_exhelper_panel', function (Blueprint $table) {
            $table->dropColumn('begin_time');
            $table->dropColumn('end_time');
        });
    }

}