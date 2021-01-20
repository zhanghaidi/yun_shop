<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateImsYzGoodsPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_package')) {
            Schema::create('yz_goods_package', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->string('title');
                $table->string('thumb')->default('');
                $table->decimal('on_sale_price')->default(0.00);
                $table->boolean('limit_time_status')->default(0);
                $table->integer('start_time')->nullable();
                $table->integer('end_time')->nullable();
                $table->boolean('other_package_status')->default(0);
                $table->string('other_package_ids')->default('');

                $table->boolean('status')->default(0);
                $table->string('share_title')->default('');
                $table->string('share_thumb')->default('');
                $table->string('share_desc')->default('');
                $table->string('description_title')->default('');
                $table->string('description_thumb')->default('');
                $table->string('description_desc')->default('');

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
        Schema::drop('yz_goods_package');
    }
}
