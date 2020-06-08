<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoomToYzOrderIncomeCountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_order_income_count')) {
            Schema::table('yz_order_income_count',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_order_income_count', 'room')) {
                        $table->decimal('room',14,2)->nullable()->comment('主播分红');
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
