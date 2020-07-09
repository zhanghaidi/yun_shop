<?php
namespace Yunshop\Commission\widgets;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/18
 * Time: 下午5:36
 */

use app\common\components\Widget;
use app\common\facades\Setting;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Commission;

class CommissionWidget extends Widget
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function run()
    {
        $set = Setting::get('plugin.commission');
        $item = Commission::getGoodsSet($this->goods_id);
        $item->rule = unserialize($item->rule);
        $levels = AgentLevel::getLevels()->get();
        return view('Yunshop\Commission::admin.goods', [
            'set' => $set,
            'item' => $item,
            'levels' => $levels,
            'defaultLevel' => AgentLevel::getDefaultLevelName()
        ])->render();
    }
}