<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzLoveGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_love_goods')) {
            Schema::table('yz_love_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_love_goods', 'parent_award')) {
                    $table->decimal('parent_award', 10);
                }
                if (!Schema::hasColumn('yz_love_goods', 'parent_award_proportion')) {
                    $table->decimal('parent_award_proportion', 10);
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
