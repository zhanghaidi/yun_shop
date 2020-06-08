<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHkScanPayToYzPayTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_pay_type')) {
            \Illuminate\Support\Facades\DB::insert('INSERT INTO `'.app('db')->getTablePrefix().'yz_pay_type` (`id`, `name`, `code`, `setting_key`, `type`, `plugin_id`, `unit`, `updated_at`, `created_at`, `deleted_at`, `need_password`)
            VALUES
                (56, \'微信香港钱包(扫码支付)\', \'HkScanPay\', \'shop.hk_scan_pay\', 2, 0, \'元\', 1584689274, NULL, NULL, 0)
            ');
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
