<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertColumnsToImsYzSupplierTable extends Migration
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
                if (!Schema::hasColumn('yz_supplier', 'bank_username')) {
                    $table->string('bank_username', 255)->default('');
                }
                if (!Schema::hasColumn('yz_supplier', 'bank_of_accounts')) {
                    $table->string('bank_of_accounts', 255)->default('');
                }
                if (!Schema::hasColumn('yz_supplier', 'opening_branch')) {
                    $table->string('opening_branch', 255)->default('');
                }
                if (!Schema::hasColumn('yz_supplier', 'company_ali_username')) {
                    $table->string('company_ali_username', 255)->default('');
                }
                if (!Schema::hasColumn('yz_supplier', 'ali_username')) {
                    $table->string('ali_username', 255)->default('');
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
            $table->dropColumn('bank_username');
            $table->dropColumn('bank_of_accounts');
            $table->dropColumn('opening_branch');
            $table->dropColumn('company_ali_username');
            $table->dropColumn('ali_username');
        });
    }
}
