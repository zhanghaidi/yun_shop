<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\MinApp;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }
    protected function setMenuConfig()
    {
        /**
         * 菜单、路由、权限
         */
        \app\backend\modules\menu\Menu::current()->setPluginMenu('min-app', \Yunshop\MinApp\Common\Config\MenuHook::menu());
    }

    public function boot()
    {
        $events = app('events');
        /**
         * 发货(同步)
         */
        $events->subscribe(\Yunshop\MinApp\Common\Listeners\AfterOrderCreatedListener::class);

        /**
         * 支付完成（异步）
         */
        $events->subscribe(\Yunshop\MinApp\Common\Listeners\OrderPaidListener::class);

        /**
         * 订单完成（异步）
         */
        $events->subscribe(\Yunshop\MinApp\Common\Listeners\OrderReceiveListener::class);

        /**
         * 购物车添加
         */
        $events->subscribe(\Yunshop\MinApp\Common\Listeners\AddCartListener::class);
    }

}