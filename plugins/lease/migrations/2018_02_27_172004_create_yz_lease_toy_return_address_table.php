<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzLeaseToyReturnAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_lease_toy_return_address')) {
            Schema::create('yz_lease_toy_return_address', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('province_id')->default(0)->comment('省id');
                $table->integer('city_id')->default(0)->comment('市id');
                $table->integer('district_id')->default(0)->comment('区id');
                $table->string('contact_name', 50)->default('')->comment('联系人');
                $table->string('mobile', 30)->default(0)->comment('联系方式');
                $table->string('zip_code', 10)->default(0)->comment('邮编');
                $table->string('address', 255)->default('')->comment('详细地址');
                $table->tinyInteger('is_default')->default(0)->comment('默认 0：否 1：是');
                $table->integer('created_at')->nullable();
                $table->integer('deleted_at')->nullable();
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
        Schema::dropIfExists('yz_lease_toy_return_address');
        
    }
}
