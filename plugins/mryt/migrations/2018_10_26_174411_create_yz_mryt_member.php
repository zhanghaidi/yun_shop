<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzMrytMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_mryt_member')) {
            Schema::create('yz_mryt_member', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('uid')->default(0);
                $table->string('realname', 50)->nulllable();
                $table->string('mobile', 11)->nulllable();
                $table->integer('level');
                $table->decimal('direct', 15, 2)->default(0);
                $table->decimal('team_manage', 15, 2)->default(0);
                $table->decimal('team', 15, 2)->default(0);
                $table->decimal('thankful', 15, 2)->default(0);
                $table->decimal('train', 15, 2)->default(0);
                $table->tinyInteger('status')->default(0);
                $table->integer('created_at')->unsigned()->nullable();
                $table->integer('updated_at')->unsigned()->nullable();
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
        Schema::dropIfExists('yz_mryt_member');
    }
}
