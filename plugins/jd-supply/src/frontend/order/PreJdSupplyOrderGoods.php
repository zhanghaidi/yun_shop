<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/5
 * Time: 15:35
 */

namespace Yunshop\JdSupply\frontend\order;

use app\common\models\Order;
use Yunshop\JdSupply\models\JdSupplyOrderGoods;

class PreJdSupplyOrderGoods extends JdSupplyOrderGoods
{
    public $order;

    public function setOrder($order){
        $this->order = $order;
    }
}