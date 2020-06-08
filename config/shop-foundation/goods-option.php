<?php
return [
    'dealPrice' => [
        [
            'key' => 'goodsDealPrice',
            'class' => \app\common\helpers\Serializer::serialize(function (\app\common\models\GoodsOption $goodsOption, $param = []) {
                return new \app\common\modules\goodsOption\dealPrice\GoodsDealPrice($goodsOption);
            }),
        ], [
            'key' => 'marketDealPrice',
            'class' => \app\common\helpers\Serializer::serialize(function (\app\common\models\GoodsOption $goodsOption, $param = []) {
                return new \app\common\modules\goodsOption\dealPrice\MarketDealPrice($goodsOption);
            }),
        ]
    ]
];