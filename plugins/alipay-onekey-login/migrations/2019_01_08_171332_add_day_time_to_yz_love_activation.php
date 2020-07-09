<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDayTimeToYzLoveActivation extends Migration
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
                if (!Schema::hasColumn('yz_love_activation', 'day_time')) {
                    $table->string('day_time',20)->nullable();
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
