<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:08
 */

namespace Yunshop\Supplier\common\services\goods;


use Yunshop\Supplier\common\models\Goods;

class EditGoodsService extends \app\backend\modules\goods\services\EditGoodsService
{
    public function __construct($goods_id, $request, $type = 0)
    {
        $this->type = $type;
        $this->goods_id = $goods_id;
        $this->request = $request;
        $this->goods_model = Goods::with(['hasManyParams' => function ($query) {
                return $query->orderBy('displayorder', 'asc');
            }])->with(['hasManySpecs' => function ($query) {
                return $query->orderBy('display_order', 'asc');
            }])->with('hasManyGoodsCategory')->find($goods_id);
    }
}