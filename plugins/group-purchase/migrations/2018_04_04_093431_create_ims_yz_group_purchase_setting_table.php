<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzGroupPurchaseSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_group_purchase_setting')) {
            Schema::create('yz_group_purchase_setting', function (Blueprint $table) {
                $table->increments('id');
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
        Schema::dropIfExists('ims_yz_group_purchase_setting');
    }
}
