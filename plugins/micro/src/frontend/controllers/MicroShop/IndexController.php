<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/17
 * Time: 上午11:29
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;

use app\common\components\ApiController;
use Yunshop\Micro\common\models\MicroShop;
use Setting;
use Yunshop\Micro\common\models\MicroShopBonusLog;
use Yunshop\Micro\common\services\TimedTaskService;

class IndexController extends ApiController
{
    public function index()
    {
        $set = Setting::get('plugin.micro');
        $member_id = \YunShop::app()->getMemberId();
        $micro_shop = MicroShop::getMicroShopByMemberId($member_id);
        $all_data = [];
        $all_data['micro_shop_data'] = [
            'shop_avatar'           => $micro_shop->hasOneMember->avatar,
            'member_id'             => $member_id,
            'shop_id'               => $micro_shop->id,
            'shop_name'             => $micro_shop->shop_name,
            'level_name'            => $micro_shop->hasOneMicroShopLevel->level_name,
            'bonus_ratio'           => $micro_shop->hasOneMicroShopLevel->bonus_ratio,
            'agent_bonus_ratio'     => $set['agent_bonus_ratio']
        ];

        $all_data['order_data'] = [
            'today'         => $this->builder(1, $member_id)->count(),
            'yesterday'     => $this->builder(2, $member_id)->count(),
            'week'          => $this->builder(3, $member_id)->count(),
            'month'         => $this->builder(4, $member_id)->count(),
            'order_total'   => MicroShopBonusLog::builder()->byMemberId($member_id)->get()->count()
        ];

        $all_data['bonus_log_data'] = [
            'today'         => number_format(($this->builder(1, $member_id)->sum('bonus_money') + $this->builder(1, $member_id)->sum('lower_level_bonus_money')), 2),
            'yesterday'     => number_format(($this->builder(2, $member_id)->sum('bonus_money') + $this->builder(2, $member_id)->sum('lower_level_bonus_money')), 2),
            'week'          => number_format(($this->builder(3, $member_id)->sum('bonus_money') + $this->builder(3, $member_id)->sum('lower_level_bonus_money')), 2),
            'month'         => number_format(($this->builder(4, $member_id)->sum('bonus_money') + $this->builder(4, $member_id)->sum('lower_level_bonus_money')), 2),
            'bonus_total'   => number_format((MicroShopBonusLog::builder()->byMemberId($member_id)->get()->sum('bonus_money') + MicroShopBonusLog::builder()->byMemberId($member_id)->get()->sum('lower_level_bonus_money')), 2)
        ];

        $all_data['bonus_log_api'] = [
            'all_pai'   => 'plugin.micro.frontend.controllers.MicroShopBonusLog.list',
            'ok_api'    => 'plugin.micro.frontend.controllers.MicroShopBonusLog.list.apply',
            'no_api'    => 'plugin.micro.frontend.controllers.MicroShopBonusLog.list.apply',
            'log_data'  => MicroShopBonusLog::getBonusLogByMemberId(\YunShop::app()->getMemberId())->paginate(10)
        ];
        return $this->successJson('成功', $all_data);
    }

    public function builder($type, $member_id)
    {
        return MicroShopBonusLog::builder()->byMemberId($member_id)->byTime($type)->get();
    }
}