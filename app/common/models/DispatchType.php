<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;


/**
 * Class DispatchType
 * @package app\common\models
 * @property int id
 * @property int need_send
 * @property int code
 * @property int name
 * @property int sort
 * @property int disable
 */
class DispatchType extends BaseModel
{
    public $table = 'yz_dispatch_type';
    protected $guarded = ['id'];

    const EXPRESS = 1; // 快递
    const SELF_DELIVERY = 2; // 自提
    const STORE_DELIVERY = 3; // 门店配送
    const HOTEL_CHECK_IN = 4; // 酒店入住
    const DELIVERY_STATION_SELF = 5; // 配送站自提

    const DELIVERY_STATION_SEND = 6; // 配送站送货

    const DRIVER_DELIVERY = 7; //司机配送

    const PACKAGE_DELIVER = 8; //自提点

    public function needSend()
    {
        return $this->need_send;
    }

    public static function dispatchTypesSetting($dispatchTypes)
    {
        $dispatchTypes = array_combine(array_column($dispatchTypes, 'code'), $dispatchTypes);

        $dispatchTypesSetting = \Setting::get('goods.dispatch_types') ?: [];

        foreach ($dispatchTypes as &$dispatchType) {

            $dispatchType = array_merge($dispatchType, $dispatchTypesSetting[$dispatchType['code']]?:[]);
        }

        $dispatchTypesSetting = array_sort($dispatchTypes, function ($dispatchType) {
            return $dispatchType['sort'] + $dispatchType['id'] / 100;
        });


        return $dispatchTypesSetting;
    }

}