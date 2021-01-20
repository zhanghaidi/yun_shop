<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzLeaseToyReturnExpressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_lease_toy_return_express')) {
            Schema::create('yz_lease_toy_return_express', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_id')->default(0)->index('idx_order_id');
                $table->integer('lease_id')->default(0)->index('idx_lease_id');
                $table->string('express_company_name', 50)->default('0');
                $table->string('express_sn', 50)->default('0');
                $table->string('express_code', 20)->default('0');
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
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
        Schema::dropIfExists('yz_lease_toy_return_express');
    }
}
