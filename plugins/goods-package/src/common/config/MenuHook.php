<?php
/****************************************************************
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\GoodsPackage\common\config;

class MenuHook
{
    public static function menu()
    {
        return [
            'name' => '商品套餐',
            'type' => 'marketing',
            'url' => 'plugin.goods-package.admin.package.index',
            'url_params' => '',
            'permit' => 1,
            'menu' => 1,
            'top_show'    => 0,
            'left_first_show'   => 0,
            'left_second_show'   => 1,
            'icon' => 'fa-picture-o',
            'list_icon' => 'poster',
            'parents' => [],
            'child' => [
                'goods_package_index' => [
                    'name' => '套餐管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.goods-package.admin.package.index',
                    'url_params' => '',
                    'parents' => ['goods_package'],
                    'child' => [
                        'goods_package_index_search' => [
                            'name' => '搜索',
                            'url' => 'plugin.goods-package.admin.package.index',
                            'permit' => 1,
                            'menu' => 0,
                            'parents' => ['goods_package', 'goods_package_index'],
                        ],
                        'goods_package_index_create' => [
                            'name' => '添加套餐',
                            'url' => 'plugin.goods-package.admin.package.create',
                            'permit' => 1,
                            'menu' => 0,
                            'parents' => ['goods_package', 'area_dividend_set'],
                        ],
                        'goods_package_index_delete' => [
                            'name' => '添加套餐',
                            'url' => 'plugin.goods-package.admin.package.delete',
                            'permit' => 1,
                            'menu' => 0,
                            'parents' => ['goods_package', 'area_dividend_set'],
                        ],
                        'goods_package_index_edit' => [
                            'name' => '添加套餐',
                            'url' => 'plugin.goods-package.admin.package.edit',
                            'permit' => 1,
                            'menu' => 0,
                            'parents' => ['goods_package', 'area_dividend_set'],
                        ]
                    ]
                ],
                'goods_package_create' => [
                    'name' => '添加套餐',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.goods-package.admin.package.create',
                    'url_params' => '',
                    'parents' => ['goods_package'],
                    'child' => [],
                ],
            ],
        ];
    }

}