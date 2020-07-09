<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzLoveActivation extends Migration
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
                if (!Schema::hasColumn('yz_love_activation', 'fixed_proportion')) {
                    $table->decimal('fixed_proportion', 10);
                }
                if (!Schema::hasColumn('yz_love_activation', 'fixed_activation_love')) {
                    $table->decimal('fixed_activation_love', 10);
                }
                if (!Schema::hasColumn('yz_love_activation', 'member_froze_love')) {
                    $table->decimal('member_froze_love', 10);
                }
                if (!Schema::hasColumn('yz_love_activation', 'surplus_froze_love')) {
                    $table->decimal('surplus_froze_love', 10);
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
