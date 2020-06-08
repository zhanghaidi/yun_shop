<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 10:27 AM
 */
return [
    [
        'class' => \app\common\helpers\Serializer::serialize(function (\app\frontend\modules\order\models\PreOrder $order) {
            return new \app\frontend\modules\order\OrderGoodsPriceNode($order, 1000);
        }),
    ],

    [
        'class' => \app\common\helpers\Serializer::serialize(function (\app\frontend\modules\order\models\PreOrder $order) {
            return new \app\frontend\modules\order\OrderDispatchPriceNode($order, 3000);
        }),
    ],
    [
        'class' => \app\common\helpers\Serializer::serialize(function (\app\frontend\modules\order\models\PreOrder $order) {
            return new \app\frontend\modules\order\OrderFeeNode($order, 9200);
        }),
    ]
];