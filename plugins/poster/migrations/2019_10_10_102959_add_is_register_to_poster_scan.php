<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsRegisterToPosterScan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_poster_scan')) {
            Schema::table('yz_poster_scan', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_poster_scan', 'is_register')) {
                    $table->boolean('is_register')->nullable();
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
