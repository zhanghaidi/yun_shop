<?php

namespace Yunshop\JdSupply\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\JdSupply\services\JdGoodsService;
use Yunshop\JdSupply\services\JdOrderService;

class PushMessage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $uniacid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$uniacid)
    {
        //
        $this->data = $data;
        $this->uniacid = $uniacid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        \YunShop::app()->uniacid = $this->uniacid;
        \Setting::$uniqueAccountId = $this->uniacid;
        \Log::debug('聚合供应链推送消息',$data);
        switch ($data['type']) {
            case 'goods.price.alter':
                $bool = JdGoodsService::updatePrice($data['data']);
                break;
            case 'goods.alter':
                $bool = JdGoodsService::updateOption($data['data']);
                break;
            case 'goods.on.sale':
                $bool = JdGoodsService::updateStatus($data['data'],1);
                break;
            case 'goods.undercarriage':
                $bool = JdGoodsService::updateStatus($data['data'],0);
                break;
            case 'goods.storage.delete':
                $bool = JdGoodsService::delGoods($data['data']);
                break;
            case 'order.cancel':
                $bool = JdOrderService::cancel($data['data']);
                break;
            case 'order.delivery':
                $bool = JdOrderService::send($data['data']);
                break;
            default:
                $bool = true;
        }
    }
}
