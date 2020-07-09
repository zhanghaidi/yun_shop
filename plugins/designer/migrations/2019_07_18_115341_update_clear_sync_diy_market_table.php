<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\DB;

class UpdateClearSyncDiyMarketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('diy_market_sync')) {
            $sql = 'DELETE  from '.DB::getTablePrefix().'diy_market_sync WHERE id > 0';
            $result = DB::select($sql);
            \Log::info('diy模板云端清空数据库');
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
