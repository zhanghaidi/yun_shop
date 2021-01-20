<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToYzMrytLevel extends Migration
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
                if (!Schema::hasColumn('yz_mryt_level', 'tier')) {
                    $table->integer('tier');
                }
                if (!Schema::hasColumn('yz_mryt_level', 'tier_amount')) {
                    $table->decimal('tier_amount', 10, 2);
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
                if (Schema::hasColumn('yz_mryt_level', 'tier')) {
                    $table->dropColumn('tier');
                }
                if (Schema::hasColumn('yz_mryt_level', 'tier_amount')) {
                    $table->dropColumn('tier_amount');
                }
            });
        }
    }
}
