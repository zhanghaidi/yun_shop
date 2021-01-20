<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzDrawPrizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_draw_prize')) {
            Schema::create('yz_draw_prize', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('activity_id')->nullable();
                $table->string('name')->nullable();
                $table->tinyInteger('type')->nullable()->comment('类型');
                $table->integer('coupon_id')->nullable()->comment('优惠卷id');
                $table->integer('point')->nullable();
                $table->integer('love')->nullable();
                $table->integer('amount')->nullable()->comment('余额');
                $table->string('thumb')->nullable();
                $table->string('thumb_url')->nullable();
                $table->integer('prize_num')->nullable()->comment('奖品数量');
                $table->float('chance', '14', '2')->nullable()->comment('中奖概率');
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
