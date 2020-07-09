<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixDataInYzOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_options')) {
            $fix =  app('db')->getTablePrefix();
            \Illuminate\Support\Facades\DB::update("update ".$fix."yz_options set option_value = 'https://yun.yunzmall.com/plugin.json' where option_name = 'market_source' ");
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
