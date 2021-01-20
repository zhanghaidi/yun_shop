<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContractFieldToYzMrytLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_mryt_level')) {
            Schema::table('yz_mryt_level', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_mryt_level', 'contract')) {
                    $table->tinyInteger('contract')->default(0);
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
        if (Schema::hasTable('yz_mryt_level')) {
            Schema::table('yz_mryt_level', function (Blueprint $table) {
                if (Schema::hasColumn('yz_mryt_level', 'contract')) {
                    $table->dropColumn('contract');
                }
            });
        }
    }
}
