<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzExhelperElectronicTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_exhelper_electronic_template')) {
            Schema::create('yz_exhelper_electronic_template', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->string('order_sn');
                $table->text('print_template');
                $table->string('mark_destination');
                $table->string('logistic_code');
                $table->string('shipper_code');
                $table->string('order_code');
                $table->string('kdn_order_code');
                $table->string('package_code');
                $table->string('sorting_code');

                $table->integer('sub_count');
                $table->integer('ebusiness_id');
                $table->string('uniquer_request_number');
                $table->string('result_code');
                $table->string('reason');
                $table->boolean('success');
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
