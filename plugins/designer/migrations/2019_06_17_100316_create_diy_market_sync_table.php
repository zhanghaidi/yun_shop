<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiyMarketSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if (!Schema::hasTable('diy_market_sync')) {
                Schema::create('diy_market_sync', function (Blueprint $table) {
                    $table->integer('id', true);
                    $table->integer('sync_id')->index('idx_uniacid');
                    $table->string('title',25);
                    $table->string('category',25);
                    $table->string('page',25);
                    $table->string('type',25);
                    $table->string('thumb_url',255);
                    $table->boolean('data')->default(0)->comment('1:已拉取，0:未拉取');
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
    }
}
