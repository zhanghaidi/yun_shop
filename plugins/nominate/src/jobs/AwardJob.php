<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/18
 * Time: 10:55 AM
 */

namespace Yunshop\Nominate\jobs;


use app\common\models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Yunshop\Nominate\services\AwardSettlement;
use Yunshop\Nominate\services\TeamManagePrize;

class AwardJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        if ($this->order->status != Order::COMPLETE) {

            //file_put_contents(storage_path('logs/zxz.txt'), print_r(date('Ymd His').'orderid['.$this->order->id.']产生奖励'.PHP_EOL,1), FILE_APPEND);

            // 产生奖励
            $awardService = new TeamManagePrize($this->order);
            $awardService->handle();
        } else {
            // 奖励结算
            $settlementService = new AwardSettlement($this->order);
            $settlementService->handle();
        }
    }
}