<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddresaddressAddArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_address')) {

            $city = \app\common\models\Address::where( ['areaname' => '长春市', 'parentid' => 220000, 'level' => 2])->first();

            if ($city) {
                $aaa = \app\common\models\Address::where(['areaname' => '经开区', 'parentid'=> $city->id, 'level' => 3])->first();
                if(is_null($aaa)) {
                    \app\common\models\Address::insert(['areaname' => '经开区', 'parentid'=> $city->id, 'level' => 3]);
                }
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
