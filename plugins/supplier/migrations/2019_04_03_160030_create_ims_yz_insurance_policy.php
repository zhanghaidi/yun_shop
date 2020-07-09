<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzInsurancePolicy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_insurance_policy')) {
            Schema::create('yz_insurance_policy', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->comment('公众号ID');
                $table->integer('supplier_id')->nullable()->comment('供应商ID');
                $table->integer('serial_number')->nullable()->comment('序号');
                $table->string('shop_name',255)->nullable()->comment('店面名称');
                $table->string('insured',25)->nullable()->comment('被保人');
                $table->string('identification_number',30)->nullable()->comment('证件号码');
                $table->string('phone',11)->nullable()->comment('联系方式');
                $table->string('province_id',50)->nullable()->comment('省');
                $table->string('city_id',50)->nullable()->comment('市');
                $table->string('district_id',50)->nullable()->comment('区');
                $table->string('street_id',50)->nullable()->comment('街');
                $table->string('address',255)->nullable()->comment('详细地址');
                $table->string('insured_property',255)->nullable()->comment('投保财产');
                $table->string('customer_type',50)->nullable()->comment('投保类型');
                $table->integer('insured_amount')->nullable()->comment('保额');
                $table->integer('guarantee_period')->nullable()->comment('保期');
                $table->integer('premium')->nullable()->comment('保费');
                $table->string('insurance_company',100)->nullable()->comment('保险公司');
                $table->text('note')->nullable()->comment('备注');
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
        //
    }
}
