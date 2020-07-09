<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzMemberSynchroLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_member_synchro_log')) {
            Schema::create('yz_member_synchro_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type', 50);
                $table->string('desc', 100)->default('');
                $table->integer('old_member');
                $table->integer('new_member');
                $table->tinyInteger('status')->unllable()->default(0);
                $table->integer('created_at')->unsigned()->default(0);
                $table->integer('updated_at')->unsigned()->default(0);
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
        Schema::dropIfExists('yz_member_synchro_log');
    }
}
