<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzPluginArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_plugin_article')) {
            Schema::table('yz_plugin_article', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_plugin_article', 'type')) {
                    $table->integer('type')->nullable()->default(0)->comment('文章类型');
                }
                if (!Schema::hasColumn('yz_plugin_article', 'display_order')) {
                    $table->integer('display_order')->nullable()->comment('排序');
                }
                if (!Schema::hasColumn('yz_plugin_article', 'audio_link')) {
                    $table->string('audio_link')->nullable()->comment('音频链接');
                }
                if (!Schema::hasColumn('yz_plugin_article', 'show_levels')) {
                    $table->text('show_levels')->nullable()->comment('会员等级浏览权限');
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
