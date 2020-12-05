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
            'icon'          => 'fa-calendar',
            'list_icon'     => 'sign',
            'item'          => 'sign',
            'parents'       => [],
            'child'         => [

                'sign_records' => [
                    'name'              => trans('Yunshop\Sign::sign.sign_records'),
                    'url'               => 'plugin.sign.Backend.Modules.Sign.Controllers.sign.index',
                    'url_params'        => '',
                    'permit'            => 1,
                    'menu'              => 1,
                    'icon'              => 'fa-file-text',
                    'item'              => 'sign_records',
                    'parents'           => ['sign'],
                    'child'             => [

                        'sign_records_see' => [
                            'name'              => trans('Yunshop\Sign::sign.see_records'),
                            'url'               => 'plugin.sign.Backend.Modules.Sign.Controllers.sign.index',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'sign_records_see',
                            'parents'           => ['sign','sign_records'],
                        ],

                        'sign_records_detail' => [
                            'name'              => trans('Yunshop\Sign::sign.see_detail'),
                            'url'               => 'plugin.sign.Backend.Modules.Sign.Controllers.sign-log.index',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'sign_records_detail',
                            'parents'           => ['sign','sign_records'],
                        ],

                        'sign_records_export' => [
                            'name'              => trans('Yunshop\Sign::sign.export_records'),
                            'url'               => 'plugin.sign.Backend.Modules.Sign.Controllers.sign.export',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'sign_records_export',
                            'parents'           => ['sign','sign_records'],
                        ],
                    ]
                ],

                'base_set' => [
                    'name'              => trans('Yunshop\Sign::sign.base_set_title'),
                    'url'               => 'plugin.sign.Backend.Controllers.base-set.see',
                    'url_params'        => '',
                    'permit'            => 1,
                    'menu'              => 1,
                    'icon'              => 'fa-gears',
                    'item'              => 'base_set',
                    'parents'           => ['sign'],
                    'child'             => [

                        'base_set_see' => [
                            'name'              => trans('Yunshop\Sign::sign.see_set'),
                            'url'               => 'plugin.sign.Backend.Controllers.base-set.see',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'base_set_see',
                            'parents'           => ['sign','base_set'],
                        ],

                        'base_set_store' => [
                            'name'              => trans('Yunshop\Sign::sign.update_set'),
                            'url'               => 'plugin.sign.Backend.Controllers.base-set.store',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'base_set_store',
                            'parents'           => ['sign'],
                        ],
                    ],
                ],

                'share_set' => [
                    'name'              => trans('Yunshop\Sign::sign.share_set_title'),
                    'url'               => 'plugin.sign.Backend.Controllers.share-set.see',
                    'url_params'        => '',
                    'permit'            => 1,
                    'menu'              => 0,
                    'icon'              => 'fa-gears',
                    'item'              => 'share_set',
                    'parents'           => ['sign'],
                    'child'             => [

                        'share_set_see' => [
                            'name'              => trans('Yunshop\Sign::sign.see_set'),
                            'url'               => 'plugin.sign.Backend.Controllers.share-set.see',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'share_set_see',
                            'parents'           => ['sign'],
                        ],

                        'share_set_store' => [
                            'name'              => trans('Yunshop\Sign::sign.update_set'),
                            'url'               => 'plugin.sign.Backend.Controllers.share-set.store',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'share_set_store',
                            'parents'           => ['sign'],
                        ],
                    ]
                ],

                'explain_set' => [
                    'name'              => trans('Yunshop\Sign::sign.explain_set_title'),
                    'url'               => 'plugin.sign.Backend.Controllers.explain-set.see',
                    'url_params'        => '',
                    'permit'            => 1,
                    'menu'              => 0,
                    'icon'              => 'fa-gears',
                    'item'              => 'explain_set',
                    'parents'           => ['sign'],
                    'child'             => [

                        'explain_set_see' => [
                            'name'              => trans('Yunshop\Sign::sign.see_set'),
                            'url'               => 'plugin.sign.Backend.Controllers.explain-set.see',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'explain_set_see',
                            'parents'           => ['sign'],
                        ],

                        'explain_set_store' => [
                            'name'              => trans('Yunshop\Sign::sign.update_set'),
                            'url'               => 'plugin.sign.Backend.Controllers.explain-set.store',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'explain_set_store',
                            'parents'           => ['sign'],
                        ],
                    ]
                ],

                'notice_set' => [
                    'name'              => trans('Yunshop\Sign::sign.notice_set_title'),
                    'url'               => 'plugin.sign.Backend.Controllers.notice-set.see',
                    'url_params'        => '',
                    'permit'            => 1,
                    'menu'              => 0,
                    'icon'              => 'fa-gears',
                    'item'              => 'notice_set',
                    'parents'           => ['sign'],
                    'child'             => [

                        'notice_set_see' => [
                            'name'              => trans('Yunshop\Sign::sign.see_set'),
                            'url'               => 'plugin.sign.Backend.Controllers.notice-set.see',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'notice_set_see',
                            'parents'           => ['sign'],
                        ],

                        'notice_set_store' => [
                            'name'              => trans('Yunshop\Sign::sign.update_set'),
                            'url'               => 'plugin.sign.Backend.Controllers.notice-set.store',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => 'notice_set_store',
                            'parents'           => ['sign'],
                        ],
                    ]
                ],
            ]
        ];

        return $menu;
    }

}
