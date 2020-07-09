<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/10
 * Time: 14:24
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToImsYzExhelperSys extends Migration
{
    public function up()
    {
        if (Schema::hasTable('yz_exhelper_sys')) {
            Schema::table('yz_exhelper_sys',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_exhelper_sys', 'name')) {
                        $table->string('name', 255);
                    }
                });
        }
    }

    public function down()
    {
        Schema::table('yz_exhelper_sys', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

}