<?php
namespace Yunshop\JdSupply\frontend;

use app\common\components\BaseController;
use Yunshop\JdSupply\Jobs\PushMessage;
use Yunshop\JdSupply\services\JdGoodsService;
use Yunshop\JdSupply\services\JdOrderService;

class MessageApiController extends BaseController
{
    public function index()
    {
        $data = request()->getContent();
        $data = json_decode($data,true);
        //$this->dispatch(new PushMessage($data,\YunShop::app()->uniacid));
        \Log::debug('聚合供应链推送消息',$data);
        switch ($data['type']) {
            case 'goods.price.alter':
                $bool = JdGoodsService::updatePrice($data['data']);
                break;
            case 'goods.alter':
                $bool = JdGoodsService::updateOption($data['data']);
                break;
            case 'goods.on.sale':
                $bool = JdGoodsService::updateOnSale($data['data']);
                break;
            case 'goods.undercarriage':
                $bool = JdGoodsService::updateUnderCarriage($data['data']);
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
        if ($bool) {
            return response()->json(['code' => 1]);
        }
    }
}