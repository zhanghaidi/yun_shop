<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImsYzAddCommissionPayToImsYzAgents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_agents', function (Blueprint $table) {

            if (!Schema::hasColumn('yz_agents', 'commission_pay')) {
                $table->decimal('commission_pay', 14)->nullable()->default(0.00);
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
        Schema::table('yz_agents', function (Blueprint $table) {
            $table->dropColumn('commission_pay');
        });
    }
}
