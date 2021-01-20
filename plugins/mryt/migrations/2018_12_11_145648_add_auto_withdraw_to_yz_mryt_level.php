<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoWithdrawToYzMrytLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_mryt_level')) {
            Schema::table('yz_mryt_level', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_mryt_level', 'auto_withdraw')) {
                    $table->tinyInteger('auto_withdraw');
                }
                if (!Schema::hasColumn('yz_mryt_level', 'withdraw_time')) {
                    $table->integer('withdraw_time');
                }
                if (!Schema::hasColumn('yz_mryt_level', 'current_md')) {
                    $table->string('current_md');
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
    }
}
