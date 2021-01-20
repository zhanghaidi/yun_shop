<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzLeaseToyLevelRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_lease_toy_level_rights')) {
            Schema::create('yz_lease_toy_level_rights', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('level_id')->nullable()->default(0)->comment('等级id');
                $table->string('rent_free', 30)->nullable()->default(0)->comment('免租金');
                $table->string('deposit_free', 30)->nullable()->default(0)->comment('免押金');
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
        Schema::dropIfExists('yz_lease_toy_level_rights');
    }
}
