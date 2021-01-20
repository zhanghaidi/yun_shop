<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzLeaseToyOrderReturnAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_lease_toy_order_return_address')) {
            Schema::create('yz_lease_toy_order_return_address', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id')->default(0);
                $table->integer('lease_id')->default(0);
                $table->string('address')->default('0');
                $table->string('mobile', 20)->default('');
                $table->string('realname', 50)->default('');
                $table->integer('province_id')->default(0);
                $table->integer('city_id')->default(0);
                $table->integer('district_id')->default(0);
                $table->integer('updated_at');
                $table->integer('created_at');
                $table->integer('deleted_at')->nullable();
                $table->text('note', 65535)->nullable();
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
        Schema::dropIfExists('yz_lease_toy_order_return_address');
    }
}
