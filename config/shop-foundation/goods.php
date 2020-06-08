<?php
return [
    'dealPrice' => [
        [
            'key' => 'goodsDealPrice',
            'class' => \app\common\helpers\Serializer::serialize(function (\app\common\models\Goods $goods, $param = []) {
                return new \app\common\modules\goods\dealPrice\GoodsDealPrice($goods);
            }),
        ], [
            'key' => 'marketDealPrice',
            'class' => \app\common\helpers\Serializer::serialize(function (\app\common\models\Goods $goods, $param = []) {
                return new \app\common\modules\goods\dealPrice\MarketDealPrice($goods);
            }),
        ]
    ],
    'models'=>[
        'home_page'=>\app\common\models\Goods::class,
        'goods_info'=>\app\common\models\Goods::class,
        'goods_list'=>\app\common\models\Goods::class,
        'footprint'=>\app\common\models\Goods::class,
        'collection_page'=>\app\common\models\Goods::class,
        'commodity_classification'=>\app\common\models\Goods::class,
    ],
    //标准商城默认都会显示下面这几种类型的商品
    'plugin' => [0],
];