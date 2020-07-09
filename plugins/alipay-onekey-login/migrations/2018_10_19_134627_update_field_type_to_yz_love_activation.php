<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldTypeToYzLoveActivation extends Migration
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
                if (Schema::hasColumn('yz_love_activation', 'first_proportion')) {
                    $table->decimal('first_proportion', 10)->change();
                }
                if (Schema::hasColumn('yz_love_activation', 'second_three_proportion')) {
                    $table->decimal('second_three_proportion', 10)->change();
                }
                if (Schema::hasColumn('yz_love_activation', 'second_three_fetter_proportion')) {
                    $table->decimal('second_three_fetter_proportion', 10)->change();
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
