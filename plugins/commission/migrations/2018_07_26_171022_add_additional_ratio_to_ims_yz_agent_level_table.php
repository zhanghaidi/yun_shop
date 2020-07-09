<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalRatioToImsYzAgentLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_agent_level', function(Blueprint $table)
        {
            if (!Schema::hasColumn('yz_agent_level', 'additional_ratio')) {
                $table->decimal('additional_ratio', 12)->nullable()->default(0.00);
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
            $table->dropColumn('additional_ratio');
        });
    }
}
