<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToYzMrytOrderTeamAward extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_mryt_order_team_award')) {
            Schema::table('yz_mryt_order_team_award', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_mryt_order_team_award', 'type')) {
                    $table->tinyInteger('type');
                }
                if (Schema::hasColumn('yz_mryt_order_team_award', 'log_id')) {
                    $table->string('log_id')->change();
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
