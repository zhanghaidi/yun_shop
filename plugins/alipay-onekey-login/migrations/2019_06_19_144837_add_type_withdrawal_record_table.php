<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeWithdrawalRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_love_withdrawal_record')) {
            Schema::table('yz_love_withdrawal_record', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_love_withdrawal_record', 'path')) {
                    $table->string('path')->nullable();
                }
                if (!Schema::hasColumn('yz_love_withdrawal_record', 'processing_fee_ratio')) {
                    $table->string('processing_fee_ratio')->nullable();
                }
                if (!Schema::hasColumn('yz_love_withdrawal_record', 'conversion_ratio')) {
                    $table->string('conversion_ratio')->nullable();
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
