<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/13
 * Time: 下午1:38
 */

namespace Yunshop\Supplier\common\services;


use app\frontend\modules\member\controllers\ServiceController;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\SupplierGoods;

class CustomerService
{
    public static function getCservice($goods_id,$type = '')
    {
        if (is_null($goods_id)) {
            return '';
        }
        $goods_model = SupplierGoods::getSupplierGoodsById($goods_id);
        if (!$goods_model) {
            return '';
        }
        $suppler_model = Supplier::getSupplierById($goods_model->id, 1);
        $customer_service = (new ServiceController())->supplier_set($suppler_model->uid,$type);
        if($customer_service['mark'])
        {
            unset($customer_service['mark']);
            return $customer_service;
        }
        $set = \Setting::get('plugin.supplier.meiqia[' . $suppler_model->uid . ']');
        if (is_null($set) || !$set['meiqia']) {
            return '';
        }
        return $set['meiqia'];
    }
}