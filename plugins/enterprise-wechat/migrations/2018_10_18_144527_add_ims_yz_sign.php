<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzSign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_sign')) {
            Schema::table('yz_sign',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_sign',
                        'cumulative_love')
                    ) {
                        $table->integer('cumulative_love')->nullable()->default(0);
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
        Schema::table('yz_sign', function (Blueprint $table) {
            $table->dropColumn('cumulative_love');
        });
    }
}
