<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/8/24
 * Time: 9:27
 */

namespace Yunshop\Mryt\services;


class CommonService
{

    /**
     * 获取每日一淘插件名称（自定义名称）
     * @return mixed|string
     */
    public static function getSet()
    {
        $set = \Setting::get('plugin.mryt_set');

        //自定义名称
        $set['name'] = $set['name'] ?: trans('Yunshop\Mryt::name.plugin_name');
        $set['default_level'] = $set['default_level'] ?: trans('Yunshop\Mryt::name.default_level');
        $set['referral_name'] = $set['referral_name'] ?: trans('Yunshop\Mryt::name.referral_name');
        $set['team_name'] = $set['team_name'] ?: trans('Yunshop\Mryt::name.team_name');
        $set['thanksgiving_name'] = $set['thanksgiving_name'] ?: trans('Yunshop\Mryt::name.thanksgiving_name');
        $set['parenting_name'] = $set['parenting_name'] ?: trans('Yunshop\Mryt::name.parenting_name');
        $set['teammanage_name'] = $set['teammanage_name'] ?: trans('Yunshop\Mryt::name.teammanage_name');
        $set['tier_name'] = $set['tier_name'] ?: trans('Yunshop\Mryt::name.tier_name');

        return $set;
    }

}