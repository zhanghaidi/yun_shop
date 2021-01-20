<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsernameToYzMrytMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_mryt_member')) {
            Schema::table('yz_mryt_member', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_mryt_member', 'username')) {
                    $table->string('username')->nullable();
                }
                if (!Schema::hasColumn('yz_mryt_member', 'password')) {
                    $table->string('password')->nullable();
                }
                if (!Schema::hasColumn('yz_mryt_member', 'user_uid')) {
                    $table->string('user_uid')->nullable();
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
