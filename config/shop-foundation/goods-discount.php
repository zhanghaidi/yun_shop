<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/18
 * Time: 5:49 PM
 */
return array(
    [
        'weight' => 2010,
        'class' => \app\common\helpers\Serializer::serialize(function (\app\frontend\modules\orderGoods\models\PreOrderGoods $preOrderGoods) {
            return new \app\frontend\modules\orderGoods\discount\SingleEnoughReduce($preOrderGoods);
        }),
    ], [
        'weight' => 2020,
        'class' => \app\common\helpers\Serializer::serialize(function (\app\frontend\modules\orderGoods\models\PreOrderGoods $preOrderGoods) {
            return new \app\frontend\modules\orderGoods\discount\EnoughReduce($preOrderGoods);
        }),
    ],
);