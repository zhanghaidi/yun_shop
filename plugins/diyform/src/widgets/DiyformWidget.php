<?php
namespace Yunshop\Diyform\widgets;
/**
 * Created by PhpStorm.
 * User: lin
 * Date: 2020/02/25
 * Time: 上午9:06
 */

use app\common\components\Widget;
use Yunshop\Diyform\models\DiyformOrderModel;


class DiyformWidget extends Widget
{
    public function run()
    {
        $set = DiyformOrderModel::getDiyFormByGoodsId($this->goods_id)->first();
        return view('Yunshop\Diyform::admin.goods', [
            'set'=>$set?:[],
        ])->render();
    }


}