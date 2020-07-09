<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPosterSupplementTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_poster')) {
            if (!Schema::hasColumn('yz_poster_supplement', 'recommender_award_title')) {

                Schema::table('yz_poster_supplement', function (Blueprint $table) {
                    $table->string('recommender_award_title')->nullable();
                });
            }

            if (!Schema::hasColumn('yz_poster_supplement', 'subscriber_award_title')) {

                Schema::table('yz_poster_supplement', function (Blueprint $table) {
                    $table->string('subscriber_award_title')->nullable();
                });
            }
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
