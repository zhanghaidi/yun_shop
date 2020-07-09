<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFiledUidToYzPostByWechat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_post_by_wechat')) {
            if (!Schema::hasColumn('yz_post_by_wechat', 'uid')) {
                Schema::table('yz_post_by_wechat', function (Blueprint $table) {
                    $table->integer('uid')->default(0);
                });
            }
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
