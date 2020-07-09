<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPayToInsurancePolicy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_insurance_policy')) {
            Schema::table('yz_insurance_policy', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_insurance_policy', 'is_pay')) {
                    $table->tinyInteger('is_pay')->default(0)->comment('是否支付');
                }
                if (!Schema::hasColumn('yz_insurance_policy', 'company_id')) {
                    $table->tinyInteger('company_id')->nullable()->comment('保险公司id');
                }
                if (!Schema::hasColumn('yz_insurance_policy', 'pay_type')) {
                    $table->string('pay_type')->nullable()->comment('支付方式');
                }
                if (!Schema::hasColumn('yz_insurance_policy', 'pay_time')) {
                    $table->integer('pay_time')->nullable()->comment('支付时间');
                }
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
