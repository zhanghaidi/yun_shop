<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/28
 * Time: 4:47 PM
 */

namespace Yunshop\Mryt\services;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Mryt\common\jobs\OrderAwardJob;
use Yunshop\Mryt\common\jobs\StoreOrderAwardJob;
use Yunshop\Mryt\common\jobs\UpgrateAwardJob;

class AwardService
{
    use DispatchesJobs;

    public $uid;
    public $uniacid;
    public $log;
    public $orderId;

    public function __construct($uid, $uniacid, $log, $orderId)
    {
        $this->uid = $uid;
        $this->uniacid = $uniacid;
        $this->log = $log;
        $this->orderId = $orderId;
    }

    // 会员升级
    public function upgrateAward()
    {
        $this->dispatch(new UpgrateAwardJob($this->uid, $this->uniacid, $this->orderId));
    }

    // 销售佣金
    public function logAward()
    {
        $this->dispatch(new OrderAwardJob($this->log));
    }

    // 门店订单
    public function storeAward()
    {
        $this->dispatch(new StoreOrderAwardJob($this->log));
    }
}