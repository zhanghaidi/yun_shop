<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzCustomAppElementSortTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('yz_custom_app_element_sort')) {
            Schema::create('yz_custom_app_element_sort', function (Blueprint $table) {
                $table->mediumIncrements('id');
                $table->string('name', 100)->default('')->comment('名称');
                $table->string('label', 100)->default('')->comment('唯一标识');
                $table->tinyInteger('type', false, true)->default(0)->comment('类型:1=文字,2=图片,3=文字数组,4=图片数组');
                $table->integer('created_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::drop('yz_custom_app_element_sort');
    }
}
