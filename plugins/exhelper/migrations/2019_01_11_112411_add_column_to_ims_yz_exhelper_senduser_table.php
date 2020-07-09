<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/10
 * Time: 14:24
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToImsYzExhelperSenduserTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('yz_exhelper_senduser')) {
            Schema::table('yz_exhelper_senduser',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_exhelper_senduser', 'sender_province')) {
                        $table->string('sender_province', 200);
                    }
                    if (!Schema::hasColumn('yz_exhelper_senduser', 'sender_area')) {
                        $table->string('sender_area', 200);
                    }
                    if (!Schema::hasColumn('yz_exhelper_senduser', 'sender_street')) {
                        $table->string('sender_street', 200);
                    }
                });
        }
    }

    public function down()
    {
        Schema::table('yz_exhelper_senduser', function (Blueprint $table) {
            $table->dropColumn('sender_province');
            $table->dropColumn('sender_area');
            $table->dropColumn('sender_street');
        });
    }

}