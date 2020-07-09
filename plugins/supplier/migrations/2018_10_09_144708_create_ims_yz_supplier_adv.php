<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzSupplierAdv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('yz_supplier_adv')) {
            Schema::create('yz_supplier_adv', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable()->comment('公众号ID');
                $table->text('advs')->default(null)->comment('广告位内容');
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
        Schema::dropIfExists('yz_supplier_adv');
    }
}
