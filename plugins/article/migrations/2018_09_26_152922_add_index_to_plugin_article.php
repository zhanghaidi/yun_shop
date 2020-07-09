<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToPluginArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_plugin_article')) {
            Schema::table('yz_plugin_article', function (Blueprint $table) {
                $idx = \Illuminate\Support\Facades\DB::select('show index from ' . app('db')->getTablePrefix() . 'yz_plugin_article where key_name="idx_uniacid_state"');

                if (!$idx) {
                    \Illuminate\Support\Facades\DB::statement('alter table ' . app('db')->getTablePrefix() . 'yz_plugin_article add index `idx_uniacid_state`(`uniacid`, `state`)');
                }
            });
        }

        if (Schema::hasTable('yz_plugin_article_category')) {
            Schema::table('yz_plugin_article_category', function (Blueprint $table) {
                $idx = \Illuminate\Support\Facades\DB::select('show index from ' . app('db')->getTablePrefix() . 'yz_plugin_article_category where key_name="idx_uniacid"');

                if (!$idx) {
                    \Illuminate\Support\Facades\DB::statement('alter table ' . app('db')->getTablePrefix() . 'yz_plugin_article_category add index `idx_uniacid`(`uniacid`)');
                }
            });
        }

        if (Schema::hasTable('yz_plugin_article_log')) {
            Schema::table('yz_plugin_article_log', function (Blueprint $table) {
                $idx = \Illuminate\Support\Facades\DB::select('show index from ' . app('db')->getTablePrefix() . 'yz_plugin_article_log where key_name="idx_uniacid_articleid"');

                if (!$idx) {
                    \Illuminate\Support\Facades\DB::statement('alter table ' . app('db')->getTablePrefix() . 'yz_plugin_article_log add index `idx_uniacid_articleid`(`uniacid`, `article_id`)');
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
