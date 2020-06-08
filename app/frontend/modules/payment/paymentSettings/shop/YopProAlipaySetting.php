<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/2/17
 * Time: 15:47
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class YopProAlipaySetting extends BaseSetting
{

    public function canUse()
    {
        $set = $this->getYopSet();

        return \YunShop::request()->type != 2 && \YunShop::request()->type != 7 && !empty($set) && $set['alipay_pay'] != 1;
    }

    public function exist()
    {
        $set = $this->getYopSet();

        return !empty($set['parent_private_key']);
    }

    private function getYopSet()
    {

        if (app('plugins')->isEnabled('yop-pro')) {
            return \Yunshop\YopPro\models\YopProSet::getSet();
        }


        return [];
    }
}