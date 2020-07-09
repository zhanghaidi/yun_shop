<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午7:58
 */

namespace Yunshop\Supplier\common\services;


class VerifyButton
{
    public static function button()
    {
        return [
            'value' => '供应商申请',
            'api'   => 'plugin.supplier.supplier.controllers.apply.supplier-apply.apply'
        ];
    }
}