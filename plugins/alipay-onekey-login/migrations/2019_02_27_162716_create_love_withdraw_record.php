<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoveWithdrawRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_love_withdrawal_record')) {
            Schema::create('yz_love_withdrawal_record', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->comment('公众号ID');
                $table->integer('member_id')->nullable()->comment('会员id');
                $table->string('love_value')->nullable()->comment('爱心值');
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
        Schema::dropIfExists('yz_love_withdrawal_record');
    }
}
