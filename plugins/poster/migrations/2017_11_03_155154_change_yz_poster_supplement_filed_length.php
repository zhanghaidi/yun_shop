<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeYzPosterSupplementFiledLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_poster_supplement')) {
            Schema::table('yz_poster_supplement', function (Blueprint $table) {
                if (Schema::hasColumn('yz_poster_supplement', 'recommender_coupon_name')) {
                    $table->string('recommender_coupon_name')->change();
                }

                if (Schema::hasColumn('yz_poster_supplement', 'subscriber_coupon_name')) {
                    $table->string('subscriber_coupon_name')->change();
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
