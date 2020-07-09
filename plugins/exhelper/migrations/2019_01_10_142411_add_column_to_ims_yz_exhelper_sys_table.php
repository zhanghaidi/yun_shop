<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/10
 * Time: 14:24
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToImsYzExhelperSysTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('yz_exhelper_sys')) {
            Schema::table('yz_exhelper_sys',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_exhelper_sys', 'apikey')) {
                        $table->string('apikey', 200)->nullable()->default('');
                    }
                    if (!Schema::hasColumn('yz_exhelper_sys', 'merchant_id')) {
                        $table->string('merchant_id', 200)->nullable()->default('');
                    }
                });
        }
    }

    public function down()
    {
        Schema::table('yz_exhelper_sys', function (Blueprint $table) {
            $table->dropColumn('apikey');
            $table->dropColumn('merchant_id');
        });
    }

}