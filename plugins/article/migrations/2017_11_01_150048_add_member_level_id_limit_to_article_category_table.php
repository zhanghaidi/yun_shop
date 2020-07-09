<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMemberLevelIdLimitToArticleCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_plugin_article_category')) {

            Schema::table('yz_plugin_article_category', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_plugin_article_category', 'member_level_id_limit')) {
                    $table->integer('member_level_id_limit')->nullable();
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
