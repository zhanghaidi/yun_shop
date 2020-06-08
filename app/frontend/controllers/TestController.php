<?php

namespace app\frontend\controllers;

use app\common\components\BaseController;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\Option;
use app\common\models\Order;
use app\common\modules\goods\GoodsRepository;
use app\common\modules\option\OptionRepository;
use app\frontend\models\Goods;
use Yunshop\Love\Modules\Goods\GoodsLoveRepository;

class TestController extends BaseController
{
    public function index()
    {
        $order = Order::find(45751);
        event(new AfterOrderPaidEvent($order));
//        $goods = Goods::find(1175);
//        $goods->reduceStock(1);
        return $this->successJson();
//        $goods->stock = max($goods->stock - 1, 0);
//        $goods->save();
//        $stock = file_get_contents(storage_path('app/test'));
//        $stock = max($stock - 1, 0);
//        file_put_contents(storage_path('app/test'), $stock . PHP_EOL);

    }
}