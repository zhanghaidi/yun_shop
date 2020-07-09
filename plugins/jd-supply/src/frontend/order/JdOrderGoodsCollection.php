<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/5
 * Time: 16:08
 */

namespace Yunshop\JdSupply\frontend\order;

use app\framework\Database\Eloquent\Collection;

class JdOrderGoodsCollection extends Collection
{
    public function setOrder($order){
        foreach ($this as $orderGoods){
            $orderGoods->setOrder($order);
        }
    }
}