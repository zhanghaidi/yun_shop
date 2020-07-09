<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzInsuranceCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_insurance_company')) {
            Schema::create('yz_insurance_company', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->comment('公众号ID');
                $table->integer('sort')->nullable()->comment('排序');
                $table->string('name')->nullable()->comment('保险公司名称');
                $table->tinyInteger('is_show')->default(1)->comment('是否显示');
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
