<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/9
 * Time: 8:47
 */

namespace Yunshop\Supplier\common\services;


use app\common\models\Address;
use app\common\models\Street;

class InsuranceService
{
    /**
     * 地址转换，将地址代码转换成文字
     */
    public function addressTranslation($values)
    {
        foreach ($values as $key => $itme){
            $level_1 = Address::where(['id' => $itme['province_id'],'level' => 1])->first(['areaname']);
            $level_2 = Address::where(['id' => $itme['city_id'],'level' => 2])->first(['areaname']);
            $level_3 = Address::where(['id' => $itme['district_id'],'level' => 3])->first(['areaname']);
            $level_4 = Street::where(['id' => $itme['street_id'],'level' => 4])->first(['areaname']);

            $values[$key]['province_id'] = $level_1 ? $level_1->toArray()['areaname'] : "";
            $values[$key]['city_id'] = $level_2 ? $level_2->toArray()['areaname'] : "";
            $values[$key]['district_id'] = $level_3 ? $level_3->toArray()['areaname'] : "";
            $values[$key]['street_id'] = $level_4 ? $level_4->toArray()['areaname'] : "";
        }
        return $values;
    }
}