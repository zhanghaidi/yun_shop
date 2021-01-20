<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsUsernameToYzMrytLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_mryt_level')) {
            Schema::table('yz_mryt_level', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_mryt_level', 'username')) {
                    $table->string('username')->nullable();
                }
                if (!Schema::hasColumn('yz_mryt_level', 'password')) {
                    $table->string('password')->nullable();
                }
                if (!Schema::hasColumn('yz_mryt_level', 'is_username')) {
                    $table->tinyInteger('is_username')->nullable();
                }
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
    }
}
