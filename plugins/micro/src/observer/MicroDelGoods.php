<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/18
 * Time: 下午4:17
 */
namespace Yunshop\Micro\observer;

use app\common\models\BaseModel;
use Yunshop\Micro\common\models\MicroShopGoods;

class MicroDelGoods  extends BaseModel
{
    public static function deleteMicroGoods($goods_id, $data, $operate)
    {
        if ($operate == 'deleted') {
            MicroShopGoods::where('goods_id', $goods_id)->delete();
        }
    }
}