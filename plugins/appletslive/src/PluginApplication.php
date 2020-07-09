<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-12-18
 * Time: 16:03
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Appletslive;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {

    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('appletslive', [
            'name' => '小程序直播',
            'type' => 'tool',
            'url' => 'plugin.appletslive.admin.controllers.set.index',
            'url_params' => '',
            'permit' => 1,
            'menu' => 1,
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => 'fa-heart-o',
            'list_icon' => 'appletslive',
            'item' => '',
            'parents' => [],
            'child' => [
                'appletslive-set' => [
                    'name' => '小程序直播',
                    'url' => 'plugin.appletslive.admin.controllers.set.index',
                    'url_params' => '',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'item' => '',
                    'parents' => ['appletslive'],
                    'child' => []
                ],
            ]
        ]);
    }
}