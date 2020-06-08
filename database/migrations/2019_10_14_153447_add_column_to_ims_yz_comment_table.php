<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToImsYzCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_comment')) {

            if (!Schema::hasColumn('yz_comment', 'plugin_id')) {
                Schema::table('yz_comment', function (Blueprint $table) {
                    $table->integer('plugin_id')->nullable()->comment('');
                });
            }
            if (!Schema::hasColumn('yz_comment', 'plugin_table_id')) {
                Schema::table('yz_comment', function (Blueprint $table) {
                    $table->integer('plugin_table_id')->nullable()->comment('评论归属id?平台/门店');
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
        Schema::table('yz_task_reward_activity', function (Blueprint $table) {
            $table->dropColumn('plugin_id');
            $table->dropColumn('plugin_table_id');
        });
    }
}
