<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/10
 * Time: 14:24
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToImsYzExhelperPrintTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('yz_exhelper_print')) {
            Schema::table('yz_exhelper_print',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_exhelper_print', 'panel_print_status')) {
                        $table->boolean('panel_print_status')->default(0);
                    }
                });
        }
    }

    public function down()
    {
        Schema::table('yz_exhelper_print', function (Blueprint $table) {
            $table->dropColumn('panel_print_status');
        });
    }

}