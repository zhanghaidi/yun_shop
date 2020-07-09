<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:38
 */

namespace Yunshop\JdSupply\frontend\order;

use Illuminate\Container\Container;

class OrderManager extends Container
{
    public function __construct()
    {
        $this->bind('PreOrder', function ($orderManager, $attributes) {
            return new PreOrder($attributes);
        });

    }
}