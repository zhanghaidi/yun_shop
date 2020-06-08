<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateYzAddressAddAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_address')) {




            $add_area = [
                [
                    'province_id' => 220000,
                    'province_name' => '吉林省',
                    'city_name' => '长春市',
                    'area' => ['经开区','高新区', '净月区','汽开区'],
                ],
                [
                    'province_id' => 510000,
                    'province_name' => '四川省',
                    'city_name' => '成都市',
                    'area' => ['高新区','天府新区'],
                ]
            ];

            foreach ($add_area as $k => $item) {
                $city = \app\common\models\Address::where( ['areaname' => $item['city_name'], 'parentid' => $item['province_id'], 'level' => 2])->first();
                if ($city) {
                    foreach ($item['area'] as $area_name) {
                        $aaa = \app\common\models\Address::where(['areaname' => $area_name, 'parentid'=> $city->id, 'level' => 3])->first();
                        if(is_null($aaa)) {
                            \app\common\models\Address::insert(['areaname' => $area_name, 'parentid'=> $city->id, 'level' => 3]);
                        }
                    }

                }
            }

            (new \app\common\services\address\GenerateAddressJs())->address();
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
