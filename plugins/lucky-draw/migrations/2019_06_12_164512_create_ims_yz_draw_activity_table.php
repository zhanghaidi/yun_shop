<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzDrawActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_draw_activity')) {
            Schema::create('yz_draw_activity', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->string('name')->nullable();
                $table->string('countdown_time')->nullable();
                $table->tinyInteger('role_type')->nullable()->comment('参与人身份');
                $table->tinyInteger('member_type')->nullable()->comment('会员/推广员');
                $table->string('level_id')->nullable()->comment('会员等级id');
                $table->tinyInteger('draw_type')->nullable()->comment('活动抽奖方式0无限制/1积分/2爱心值');
                $table->string('use_point')->nullable()->comment('消耗积分');
                $table->string('use_love')->nullable()->comment('消耗爱心值');
                $table->tinyInteger('partake_times')->nullable()->comment('参与次数类型0每天/1每人');
                $table->string('days_times')->nullable()->comment('每天可抽');
                $table->string('days_share_times')->nullable()->comment('每天分享获得次数');
                $table->string('somebody_times')->nullable()->comment('每人可抽');
                $table->string('somebody_share_times')->nullable()->comment('每人分享获得次数');

                $table->string('prize_id')->nullable()->comment('奖品id组');
                $table->string('empty_prize_name')->nullable()->comment('空奖名称');
                $table->string('empty_prize_thumb')->nullable()->comment('空奖图片');
                $table->string('empty_prize_prompt')->nullable()->comment('提示语');
                $table->tinyInteger('jump_type')->nullable()->comment('跳转类型0无跳转/1有跳转');
                $table->string('jump_link')->nullable()->comment('跳转链接');
                $table->string('partake_point')->nullable()->comment('参与送积分');
                $table->string('partake_love')->nullable()->comment('参与送爱心值');
                $table->string('partake_amount')->nullable()->comment('参与送余额');
                $table->string('partake_coupon_id')->nullable()->comment('参与送优惠卷');
                $table->tinyInteger('limit')->nullable()->comment('参与奖限制0未中奖者/1所有人');

                $table->string('background')->nullable()->comment('背景图');
                $table->string('background_colour')->nullable()->comment('背景色');
                $table->tinyInteger('is_logo')->nullable()->comment('商家logo不展示0/1展示');
                $table->tinyInteger('is_roster')->nullable()->comment('中奖名单不展示0/1展示');
                $table->text('content')->nullable()->comment('活动说明');

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
