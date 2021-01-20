<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/10
 * Time: 12:08
 */

namespace Yunshop\Tbk\common\jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Tbk\common\services\TaobaoService;

class CatchGoodsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $id = null;
    private $uniacid = null;

    public function __construct($id, $uniacid)
    {
        $this->id = $id;
        $this->uniacid = $uniacid;
    }

    public function handle()
    {
        \YunShop::app()->uniacid = $this->uniacid;
        \Setting::$uniqueAccountId = $this->uniacid;

        $set = \Setting::get('plugin.tbk');
        $taobao = new TaobaoService($set['appkey'], $set['secret'], $set['ad_zone_id']);
        $taobao->favourite($this->id);
    }
}