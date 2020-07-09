<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCreditToPluginArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_plugin_article')) {
            Schema::table('yz_plugin_article', function (Blueprint $table) {
                if (Schema::hasColumn('yz_plugin_article', 'credit')) {
                    $table->decimal('credit', 10)->nullable()->default(0.00)->change();
                }
                if (Schema::hasColumn('yz_plugin_article', 'point')) {
                    $table->decimal('point', 10)->nullable()->default(0.00)->change();
                }
                if (Schema::hasColumn('yz_plugin_article', 'bonus_total')) {
                    $table->decimal('bonus_total', 10)->nullable()->default(0.00)->change();
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
