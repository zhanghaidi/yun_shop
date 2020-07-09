<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzDesignerMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_designer_menu')) {
            Schema::table('yz_designer_menu', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_designer_menu', 'ingress')) {
                    $table->string('ingress', 45)->default('');
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
