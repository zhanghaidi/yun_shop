<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzSignLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_sign_log')) {
            Schema::table('yz_sign_log',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_sign_log',
                        'award_love')
                    ) {
                        $table->integer('award_love')->nullable()->default(0);
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
        Schema::table('yz_sign_log', function (Blueprint $table) {
            $table->dropColumn('award_love');
        });
    }
}
