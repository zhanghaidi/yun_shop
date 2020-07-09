<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzLoveDividendLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_love_dividend_log')) {
            Schema::create('yz_love_dividend_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->comment('公众号ID');
                $table->integer('member_id')->nullable()->comment('会员id');
                $table->string('shop_amount')->nullable()->comment('商城营业额');
                $table->string('love')->nullable()->comment('个人爱心值');
                $table->string('love_all')->nullable()->comment('总爱心值');
                $table->string('dividend_rate')->nullable()->comment('分红比例');
                $table->string('dividend')->nullable()->comment('分红');
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
        Schema::dropIfExists('yz_love_dividend_log');
    }
}
