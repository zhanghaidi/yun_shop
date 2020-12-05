<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/5 下午2:04
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Common\Config;


class MenuHook
{
    public static function menu()
    {
        $menu = [
            'name'          => trans('Yunshop\Circle::circle.plugin_name'),
            'type'          => 'marketing',
            'url'           => 'plugin.Circle.Backend.Modules.Circle.Controllers.index',
            'url_params'    => '',
            'permit'        => 1,
            'menu'          => 1,
            'top_show'      => 0,
            'left_first_show'   => 0,
            'left_second_show'  => 1,
            'icon'          => 'fa-quz',
            'list_icon'     => 'circle',
            'item'          => 'circle',
            'parents'       => [],
            'child'         => [

                'base_set' => [
                    'name'              => trans('Yunshop\Circle::circle.base_set_title'),
                    'url'               => 'plugin.Circle.Backend.Controllers.base-set.see',
                    'url_params'        => '',
                    'permit'            => 1,
                    'menu'              => 1,
                    'icon'              => 'fa-gears',
                    'item'              => 'base_set',
                    'parents'           => ['circle'],
                    'child'             => [

                    ],
                ],

            ]
        ];

        return $menu;
    }

}
