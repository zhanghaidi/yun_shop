<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsOldToYzMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_member')) {
            Schema::table('yz_member', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_member', 'is_old')) {
                    $table->tinyInteger('is_old')->default(0)->comment('老数据判断');
                }
                if (!Schema::hasColumn('yz_member', 'mark_member_id')) {
                    $table->integer('mark_member_id')->default(0)->comment('标记会员id');
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
