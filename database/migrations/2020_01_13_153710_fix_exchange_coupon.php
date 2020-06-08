<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use app\common\facades\Setting;
use app\common\models\UniAccount;

class FixExchangeCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('yz_coupon'))
        {
            $uniAccount = UniAccount::get() ?: [];
            foreach ($uniAccount as $u) {
                Setting::$uniqueAccountId = \YunShop::app()->uniacid = $u->uniacid;
                \app\common\models\Coupon::uniacid()->where('use_type',8)->update(['use_type'=>2,'coupon_method'=>3]);
            }
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
