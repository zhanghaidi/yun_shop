<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 上午10:15
 */

namespace Yunshop\Supplier\supplier\controllers\goods;

use app\common\components\Widget;
use app\backend\modules\goods\models\GoodsDispatch;
use app\backend\modules\goods\models\Dispatch;
use app\common\services\Session;
use Yunshop\Supplier\common\models\SupplierDispatch;

class GoodsWidget extends Widget
{
    public function run()
    {
        $dispatch = new GoodsDispatch();
        if ($this->goods_id && GoodsDispatch::getInfo($this->goods_id)) {
            $dispatch = GoodsDispatch::getInfo($this->goods_id);
        }
        $dispatch_templates = SupplierDispatch::getList(Session::get('supplier')['id'])->get()->toArray();
        return view('Yunshop\Supplier::supplier.goods.widget.dispatch', [
            'dispatch' => $dispatch,
            'dispatch_templates' => $dispatch_templates
        ])->render();
    }
}