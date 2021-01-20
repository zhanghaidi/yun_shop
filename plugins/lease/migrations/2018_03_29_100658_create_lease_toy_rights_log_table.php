<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaseToyRightsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_lease_toy_rights_log')) {
            Schema::create('yz_lease_toy_rights_log', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->default(0)->index('idx_uniacid');
                $table->integer('order_id')->default(0)->index('idx_order_id');
                $table->integer('member_id')->nullable()->default(0)->comment('会员id');
                $table->integer('sue_rent_free')->nullable()->default(0)->comment('已用免租金件数');
                $table->integer('sue_deposit_free')->nullable()->default(0)->comment('权益免押金件数');

                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
        Schema::dropIfExists('yz_lease_toy_rights_log');
    }
}
