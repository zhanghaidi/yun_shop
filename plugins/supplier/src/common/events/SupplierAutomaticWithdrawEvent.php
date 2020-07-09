<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/7/15
 * Time: 下午 02:05
 */

namespace Yunshop\Supplier\common\events;


use app\common\events\Event;
use Yunshop\Supplier\supplier\models\SupplierWithdraw;

class SupplierAutomaticWithdrawEvent extends Event
{
    private $withdraw;


    public function __construct(SupplierWithdraw $withdraw)
    {
        $this->withdraw = $withdraw;
    }


    public function getWithdrawModel()
    {
        return $this->withdraw;
    }
}
