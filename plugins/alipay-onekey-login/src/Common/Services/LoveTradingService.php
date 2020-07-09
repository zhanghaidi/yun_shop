<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/26
 * Time: 上午10:10
 */

namespace Yunshop\Love\Common\Services;


class LoveTradingService
{
    public static function getStatusName($data)
    {
        switch ($data) {
            case -1:
                return '回购中';
                break;
            case 0:
                return '出售中';
                break;
            case 1:
                return '已完成';
                break;
        }
    }

    public static function getTypeName($data)
    {
        $memberId = \YunShop::app()->getMemberId();
        switch ($data->type) {
            case 0:
                if (!$memberId) {
                    return '交易';
                } elseif ($memberId == $data->member_id) {
                    return '我出售的';
                } elseif ($memberId == $data->buy_id) {
                    return '我购买的';
                }else{
                    return '交易';
                }
                break;
            case 1:
                return '公司回购';
                break;
        }
    }


}