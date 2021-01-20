<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrizeNameImsYzDrawPrizeRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_draw_prize_record')) {
            Schema::table('yz_draw_prize_record', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_draw_prize_record', 'prize_name')) {
                    $table->string('prize_name')->nullable();
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
