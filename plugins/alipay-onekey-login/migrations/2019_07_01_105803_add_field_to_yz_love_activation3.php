<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzLoveActivation3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_love_activation')) {
            Schema::table('yz_love_activation', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_love_activation', 'froze_total')) {
                    $table->decimal('froze_total', 14);
                }
                if (!Schema::hasColumn('yz_love_activation', 'profit_proportion')) {
                    $table->decimal('profit_proportion', 14);
                }
                if (!Schema::hasColumn('yz_love_activation', 'cycle_order_profit')) {
                    $table->decimal('cycle_order_profit', 14);
                }
                if (!Schema::hasColumn('yz_love_activation', 'profit_activation_love')) {
                    $table->decimal('profit_activation_love', 14);
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
