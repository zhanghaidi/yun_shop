<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzJdSupplyErrorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_jd_supply_error')) {
            Schema::create('yz_jd_supply_error', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0)->nullable();
                $table->string('desc', 255)->nullable()->comment('错误描述');
                $table->string('type')->nullable()->comment('请求类型');
                $table->text('error_data')->nullable()->comment('返回错误');
                $table->text('request_data')->nullable()->comment('请求数据');
                $table->text('response_data')->nullable()->comment('返回数据');
                $table->string('mark')->nullable()->comment('预留字段');
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
        Schema::dropIfExists('yz_jd_supply_error');
    }
}
