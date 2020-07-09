<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLikedToPluginArticleLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_plugin_article_log')) {

            Schema::table('yz_plugin_article_log', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_plugin_article_log', 'liked')) {
                    $table->integer('liked')->nullable();
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
