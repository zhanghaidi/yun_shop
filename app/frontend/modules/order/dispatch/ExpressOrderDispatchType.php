<?php


namespace app\frontend\modules\order\dispatch;


use app\common\facades\Setting;

class ExpressOrderDispatchType extends OrderDispatchType
{
    public function enable()
    {
        if (Setting::get('shop.trade.is_dispatch')) {
            return false;
        }
        return parent::enable();
    }
    public function getId()
    {
        return 1;
    }
}