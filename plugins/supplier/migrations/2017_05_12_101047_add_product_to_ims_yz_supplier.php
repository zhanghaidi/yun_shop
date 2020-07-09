<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductToImsYzSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_supplier', function (Blueprint $table) {
            if (!Schema::hasColumn('yz_supplier', 'product')) {
                $table->string('product', 255)->default('0');
            }
            if (!Schema::hasColumn('yz_supplier', 'remark')) {
                $table->string('remark', 255)->default('0');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yz_supplier', function (Blueprint $table) {
            $table->dropColumn(['product', 'remark']);
        });
    }
}
