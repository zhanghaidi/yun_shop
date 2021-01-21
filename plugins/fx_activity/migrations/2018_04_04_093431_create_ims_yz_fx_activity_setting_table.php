<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzFxActivitySettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_fx_activity_setting')) {
            Schema::create('yz_fx_activity_setting', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('goods_id')->nullable();
                $table->integer('is_open')->nullable();
                $table->integer('settlememt_day')->nullable();
                $table->text('plugins')->nullable();
                $table->text('member_award_point')->nullable();
                $table->text('profit')->nullable();
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
        Schema::dropIfExists('ims_yz_fx_activity_setting');
    }
}
