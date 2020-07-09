<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 17:39
 */

namespace Yunshop\Commission\services;

use Yunshop\Commission\models\CommissionOrder;
use app\common\facades\Setting;

class ReturnFormatService
{
    private $rewardType;

    //处理数量 返回相同格式
    public function sameFormat($model)
    {
        foreach ($model as $item) {
            $data[] = $this->defaultFormat($item);
        }

        return $data;
    }

    protected function defaultFormat($data)
    {
        $res =  [
            'id'       => $data->id,
            'order_sn' => $data->order ? $data->order->order_sn : '',
            'amount'   => $data->commission,
            'order'    => $data->order ? $data->order->toArray() : [],
        ];
        if($this->rewardType)
        {
            return array_merge($res,['reward_type' => $this->rewardType]);
        }
        return $res;

    }

    //统一格式化数据返回
    public function getAdvFormat($result)
    {
        //加入到收入
        $this->setSettlement($result);

        return $this->defaultFormat($result);
    }

    public function setSettlement($result)
    {
        $times = time();

        $config = \app\backend\modules\income\Income::current()->getItem('commission');


        CommissionOrder::updatedManualStatement($result->id, $times);

        (new TimedTaskService())->updateCommission($result);

        //判断奖励类型
        $set = Setting::get('plugin.commission');
        if (isset($set['settlement_option']) && $set['settlement_option'] == 1) {
            $pointSet = Setting::get('point.set');
            (new TimedTaskService())->addPoint($result, $pointSet);
            (new TimedTaskService())->updatedWithdraw($result);
            $this->rewardType = Setting::get('shop.shop')['credit1'] ?: '积分';
        } else {
            (new TimedTaskService())->addIncome($result, $times, $config);
        }
    }
}