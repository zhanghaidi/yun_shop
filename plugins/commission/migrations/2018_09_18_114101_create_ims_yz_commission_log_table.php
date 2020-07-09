<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzCommissionLogTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_commission_log')) {
            Schema::create('yz_commission_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('agent_id')->default(0);
                $table->integer('uid')->default(0);
                $table->integer('before_level_id')->default(0);
                $table->integer('after_level_id')->default(0);
                $table->text('remark', 65535)->nullable();
                $table->string('time', 100)->nullable();
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
        Schema::drop('ims_yz_commission_log');
    }

}
