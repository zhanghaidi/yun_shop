<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNumToImsYzNominateUserTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_nominate_user_task')) {
            Schema::table('yz_nominate_user_task',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_nominate_user_task', 'num')) {
                        $table->integer('num')->nullable()->default(0);
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
        Schema::table('yz_nominate_user_task', function (Blueprint $table) {
            $table->dropColumn('num');
        });
    }
}
