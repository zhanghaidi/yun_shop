<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvsImgToPluginArticleTable extends Migration
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
                if (!Schema::hasColumn('yz_plugin_article', 'advs_img')) {
                    $table->string('advs_img','255')->nullable();
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
