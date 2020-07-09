<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */
namespace Yunshop\JdSupply\common\order\operations\member;

use app\frontend\modules\order\operations\OrderOperation;
use app\common\models\DispatchType;
use Yunshop\JdSupply\models\JdSupplyOrder;

class ExpressInfo extends \app\frontend\modules\order\operations\member\ExpressInfo
{
    public function getApi()
    {
        // todo 修改成新的几口地址
        if($this->order->plugin_id == JdSupplyOrder::PLUGIN_ID){
            return 'plugin.jd-supply.frontend.dispatch.express';
        }
        return parent::getApi();
    }

    public function getValue()
    {
        if($this->order->plugin_id == JdSupplyOrder::PLUGIN_ID){
            // todo 修改为新的按钮id 跟前端沟通
            return static::EXPRESS;
        }

        return parent::getValue();
    }

}