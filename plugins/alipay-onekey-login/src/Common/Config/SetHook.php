<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/26 下午6:09
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Config;


class SetHook
{
    public static function getSetMenu()
    {
        return [
            'tabRecharge'   => [
                'title' => '充值设置',
                'route' => 'plugin.love.Backend.Controllers.recharge-set.see',
            ],
            'tab_award'     => [
                'title' => trans('Yunshop\Love::award_set.subtitle'),
                'route' => 'plugin.love.Backend.Controllers.award-set.see',
            ],
            'tab_activation' => [
                'title' => trans('Yunshop\Love::activation_set.subtitle'),
                'route' => 'plugin.love.Backend.Controllers.activation-set.see',
            ],
            'tab_trading'    => [
                'title' => trans('Yunshop\Love::trading_set.subtitle'),
                'route' => 'plugin.love.Backend.Controllers.trading-set.see',
            ],
            'tab_return'     => [
                'title' => trans('Yunshop\Love::return_set.subtitle'),
                'route' => 'plugin.love.Backend.Controllers.return-set.index',
            ],
            'tab_withdraw'   => [
                'title' => trans('Yunshop\Love::withdraw_set.subtitle'),
                'route' => 'plugin.love.Backend.Controllers.withdraw-set.see',
            ],
            'tab_notice'     => [
                'title' => trans('Yunshop\Love::notice_set.subtitle'),
                'route' => 'plugin.love.Backend.Controllers.notice-set.see',
            ],
            'tab_dividend'   => [
                'title' => trans('Yunshop\Love::dividend_set.subtitle'),
                'route' => 'plugin.love.Backend.Controllers.dividend-set.index',
            ],
        ];
    }

}