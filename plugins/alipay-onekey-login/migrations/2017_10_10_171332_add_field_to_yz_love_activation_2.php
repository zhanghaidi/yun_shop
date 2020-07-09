<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzLoveActivation2 extends Migration
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
                if (!Schema::hasColumn('yz_love_activation', 'team_proportion')) {
                    $table->decimal('team_proportion', 10);
                }
                if (!Schema::hasColumn('yz_love_activation', 'team_activation_love')) {
                    $table->decimal('team_activation_love', 10);
                }
                if (!Schema::hasColumn('yz_love_activation', 'team_order_money')) {
                    $table->decimal('team_order_money', 10);
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
