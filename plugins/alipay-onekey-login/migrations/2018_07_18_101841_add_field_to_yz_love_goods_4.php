<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzLoveGoods4 extends Migration
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
                if (!Schema::hasColumn('yz_love_goods', 'parent_award_fixed')) {
                    $table->decimal('parent_award_fixed', 10, 2);
                    $table->decimal('second_award_fixed', 10, 2);
                    $table->decimal('third_award_fixed', 10, 2);
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
