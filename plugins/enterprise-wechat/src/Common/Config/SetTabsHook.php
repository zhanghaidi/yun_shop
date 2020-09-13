<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/5 下午4:04
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Common\Config;


class SetTabsHook
{
    public static function getSetTabs()
    {
        return [
            'tab_base' => [
                'title' => trans('Yunshop\Sign::sign.base_set_title'),
                'route' => 'plugin.sign.Backend.Controllers.base-set.see',
            ],
            'tab_share' => [
                'title' => trans('Yunshop\Sign::sign.share_set_title'),
                'route' => 'plugin.sign.Backend.Controllers.share-set.see',
            ],
            'tab_explain' => [
                'title' => trans('Yunshop\Sign::sign.explain_set_title'),
                'route' => 'plugin.sign.Backend.Controllers.explain-set.see',
            ],
            'tab_notice' => [
                'title' => trans('Yunshop\Sign::sign.notice_set_title'),
                'route' => 'plugin.sign.Backend.Controllers.notice-set.see',
            ]
        ];
    }

}
