<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToImsYzSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_supplier')) {
            Schema::table('yz_supplier', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_supplier', 'company_bank')) {
                    $table->string('company_bank', 100)->default('');
                }
                if (!Schema::hasColumn('yz_supplier', 'company_ali')) {
                    $table->string('company_ali', 100)->default('');
                }
                if (!Schema::hasColumn('yz_supplier', 'ali')) {
                    $table->string('ali', 100)->default('');
                }
                if (!Schema::hasColumn('yz_supplier', 'wechat')) {
                    $table->string('wechat', 100)->default('');
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
        Schema::table('yz_supplier', function (Blueprint $table) {
            $table->dropColumn('company_bank');
            $table->dropColumn('company_ali');
            $table->dropColumn('ali');
            $table->dropColumn('wechat');
        });
    }
}
