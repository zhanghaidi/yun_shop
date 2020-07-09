<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzSupplierSlide extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_supplier_slide')) {
            Schema::create('yz_supplier_slide', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->comment('公众号ID');
                $table->string('slide_name')->default(null)->comment('幻灯片名称');
                $table->string('link')->default(null)->comment('幻灯片链接');
                $table->string('thumb')->default(null)->comment('幻灯片图片');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->integer('display_order')->default(0)->comment('排序');
                $table->tinyInteger('enabled')->default(0)->comment('是否显示');
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
        Schema::dropIfExists('yz_supplier_slide');
    }
}
