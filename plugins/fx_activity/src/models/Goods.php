<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/22
 * Time: 15:21
 */

namespace Yunshop\FxActivity\models;

class Goods extends \app\backend\modules\goods\models\Goods
{

    public static function getGoodsData()
    {
        return [
            'uniacid'       => \YunShop::app()->uniacid,
            'type'          => 1,
            'status'        => 1,
            'display_order' => 0,
            'title'         => '活动报名',
            'thumb'         => 'http://test-1251768088.cosgz.myqcloud.com/images/3/2018/03/twe2V72PAepzd9eeD92W6SS9aAd8sE.jpg',
            'sku'           => '个',
            'market_price'  => 1,
            'price'         => 1,
            'cost_price'    => 1,
            'stock'         => '9999999',
            'weight'        => 0,
            'is_plugin'     => 0,
            'brand_id'      => 0,
            'plugin_id'     => Order::ORDER_PLUGIN_ID,
        ];
    }

    public static function saveGoods($widgets_data, $goods_model = '')
    {
        //如果未空则创建新的good
        $goods_model = $goods_model ?: new self();
        //为新的goods插入虚拟商品数据
        $goods_model->fill(self::getGoodsData());
        //更新插件设置信息
        $goods_model->widgets = (new BasisSetting())->getDeductWidgets($widgets_data);
        $goods_model->save();

        return $goods_model;
    }
}