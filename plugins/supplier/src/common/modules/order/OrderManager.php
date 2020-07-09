<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/27
 * Time: 3:55 PM
 */

namespace Yunshop\Supplier\common\modules\order;


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