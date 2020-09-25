<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\EnterpriseWechat;


use app\backend\modules\menu\Menu;
use Config;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {


        /**
         * 设置菜单 config
         */
        Config::set('plugins.sign.set_tabs', \Yunshop\Sign\Common\Config\SetTabsHook::getSetTabs());

    }

    public function getTemplateItems()
    {
        return ['sign_success_notice' => [
            'title' => trans('Yunshop\EnterpriseWechat::sign.plugin_name') . '通知',
            'subtitle' => '企业微信签到通知',
            'value' => 'sign_notice',
            'param' => [
                '昵称', '签到时间', '连签天数', '签到奖励',
            ]
        ]];
    }

    protected function setMenuConfig()
    {
        /**
         * 菜单、权限、路由
         */
        Menu::current()->setPluginMenu(['enterprise.wechat' => \Yunshop\Sign\Common\Config\MenuHook::menu()]);
    }

    public function boot()
    {

    }

}