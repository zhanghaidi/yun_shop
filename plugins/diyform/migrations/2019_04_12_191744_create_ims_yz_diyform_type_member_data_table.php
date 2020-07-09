<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzDiyformTypeMemberDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_diyform_type_member_data')) {
            Schema::create('yz_diyform_type_member_data', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0)->nullable();
                $table->integer('form_id')->default(0)->nullable();
                $table->integer('member_id')->default(0)->nullable();
                $table->integer('form_data_id')->default(0)->nullable();
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
        if (Schema::hasTable('yz_diyform_type_member_data')) {

            Schema::drop('yz_diyform_type_member_data');
        }
    }
}
