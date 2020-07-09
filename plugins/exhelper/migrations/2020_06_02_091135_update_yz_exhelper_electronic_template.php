<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateYzExhelperElectronicTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_exhelper_electronic_template')) {
            Schema::table('yz_exhelper_electronic_template',
                function (Blueprint $table) {
                    if (Schema::hasColumn('yz_exhelper_electronic_template', 'print_template')) {
                        $table->longText('print_template')->default('')->nullable()->change();
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
