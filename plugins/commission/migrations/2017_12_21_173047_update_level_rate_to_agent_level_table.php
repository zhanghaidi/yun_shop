<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLevelRateToAgentLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_agent_level')) {
            Schema::table('yz_agent_level', function (Blueprint $table) {
                if (Schema::hasColumn('yz_agent_level', 'first_level')) {
                    $table->decimal('first_level', 12)->nullable()->default(0.00)->change();
                }
                if (Schema::hasColumn('yz_agent_level', 'second_level')) {
                    $table->decimal('second_level', 12)->nullable()->default(0.00)->change();
                }
                if (Schema::hasColumn('yz_agent_level', 'third_level')) {
                    $table->decimal('third_level', 12)->nullable()->default(0.00)->change();
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
        //
    }
}
