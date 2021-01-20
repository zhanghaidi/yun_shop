<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzDrawPrizeRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_draw_prize_record')) {
            Schema::create('yz_draw_prize_record', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable();
                $table->integer('activity_id')->nullable();
                $table->string('prize_id')->nullable();
                $table->integer('point')->nullable();
                $table->integer('love')->nullable();
                $table->integer('amount')->nullable();
                $table->integer('coupon_id')->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
