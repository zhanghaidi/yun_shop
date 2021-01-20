<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/18
 * Time: 2:33 PM
 */

namespace Yunshop\Nominate\widget;


use app\common\components\Widget;
use Yunshop\Nominate\models\NominateGoods;

class NominateWidget extends Widget
{
    public function run()
    {
        $set = \Setting::get('plugin.nominate');
        $plugin_name = $set['plugin_name']?:'推荐奖励';

        $nominateGoods = NominateGoods::select()
            ->where('goods_id', $this->goods_id)
            ->first();

        return view('Yunshop\Nominate::widget.goods', [
            'plugin_name' => $plugin_name,
            'is_open' => $nominateGoods->is_open
        ])->render();
    }
}