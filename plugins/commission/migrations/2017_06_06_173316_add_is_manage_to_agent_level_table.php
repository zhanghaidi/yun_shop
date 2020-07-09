<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsManageToAgentLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_agent_level', function (Blueprint $table) {
            if (!Schema::hasColumn('yz_agent_level', 'is_manage')) {
                $table->integer('is_manage')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yz_agent_level', function (Blueprint $table) {
            $table->dropColumn('is_manage');
        });
    }
}
