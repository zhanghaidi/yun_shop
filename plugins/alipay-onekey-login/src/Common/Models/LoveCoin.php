<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 下午5:04
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;

use app\common\models\VirtualCoin;
use Yunshop\Love\Common\Services\CommonService;


/**
 * Class GoodsLove
 * @package Yunshop\Love\Common\Models
 *
 */
class LoveCoin extends VirtualCoin
{
    protected function _getExchangeRate()
    {
        $deduction_exchange = !\Setting::get('love.deduction_exchange') ? 100 : \Setting::get('love.deduction_exchange');
        return $deduction_exchange / 100;
    }

    protected function _getName()
    {
        return CommonService::getLoveName();
    }

    protected function _getCode()
    {
        return 'love';
    }
}