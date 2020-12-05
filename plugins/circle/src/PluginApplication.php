<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Circle;


use app\backend\modules\menu\Menu;
use Config;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {


        /**
         * 设置菜单 config
         */
        Config::set('plugins.circle.set_tabs', \Yunshop\Circle\Common\Config\SetTabsHook::getSetTabs());

    }

    public function getTemplateItems()
    {
        return ['circle.base' => [
            'title' => trans('Yunshop\Circle::circle.plugin_name') . '圈子',
            'subtitle' => '圈子',
            'value' => 'circle',
            'param' => [
                '圈子列表'
            ]
        ]];
    }

    protected function setMenuConfig()
    {
        /**
         * 菜单、权限、路由
         */
        Menu::current()->setPluginMenu(['circle' => \Yunshop\Circle\Common\Config\MenuHook::menu()]);
    }

    public function boot()
    {

    }

}