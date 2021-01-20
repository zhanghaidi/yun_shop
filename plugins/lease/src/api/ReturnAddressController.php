<?php

namespace Yunshop\LeaseToy\api;

use app\common\components\ApiController;
use app\common\models\Address;
use Yunshop\LeaseToy\models\ReturnAddressModel;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/9
*/
class ReturnAddressController extends ApiController
{
    
    public function index()
    {
        $list = ReturnAddressModel::uniacid()->orderBy('is_default', 'desc')->get();

        $list = $this->getMap($list);
        $this->successJson('ok', $list);
    }

     //地址处理
    private function getMap($list)
    {
        if (!$list) {
            return $list;
        }
        $list->map(function ($row){
            $province = Address::select()->where('id', $row->province_id)->first();
            $city = Address::select()->where('id', $row->city_id)->first();
            $district = Address::select()->where('id', $row->district_id)->first();
            $address = $province['areaname'] . $city['areaname'] . $district['areaname'] . $row->address;
            $row->address = $address;
        });
        return $list;
    }
}
