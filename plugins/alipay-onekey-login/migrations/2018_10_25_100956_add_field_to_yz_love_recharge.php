<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzLoveRecharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_love_recharge')) {
            Schema::table('yz_love_recharge', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_love_recharge', 'remark')) {
                    $table->string('remark', 50)->nullable();
                }
                if (!Schema::hasColumn('yz_love_recharge', 'value_type')) {
                    $table->integer('value_type')->nullable()->default(1);
                }
                if (Schema::hasColumn('yz_love_recharge', 'old_value')) {
                    $table->dropColumn('old_value', 'new_value');
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
