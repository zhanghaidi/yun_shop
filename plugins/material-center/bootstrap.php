<?php

use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events) {

    \Config::set('plugins_menu.material_center', [
                
        'name'       => '素材中心',
        'type'       => 'marketing',
        'url'        => 'plugin.material-center.admin.material.index',
        'url_params'        => '',
        'permit'            => 1,
        'menu'              => 1,
        // 'top_show'          => 1,
        'left_first_show'   => 0,
        'left_second_show'  => 1,
        'icon'              => 'fa-glide-g',
        'list_icon'         => 'material-center',
        'item'              => 'material-center',
        'parents'           => [],
        'child'             => [
            'material_list'  => [
                'name'       => '素材列表',
                'url'        => 'plugin.material-center.admin.material.index',
                'url_params' => '',
                'permit'     => 1,
                'menu'       => 1,
//                'icon'       => '',
                'sort'       => 0,
                'item'       => 'material_list',
                'parents'    => ['material_center'],
                'child'   => [
                    'material_add'  => [
                        'name'       => '添加素材',
                        'url'        => 'plugin.material-center.admin.material.add',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'material_add',
                        'parents'    => ['material_center', 'material_list'],
                    ],
                    'material_edit'  => [
                        'name'       => '修改素材',
                        'url'        => 'plugin.material-center.admin.material.edit',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'material_edit',
                        'parents'    => ['material_center', 'material_list'],
                    ],
                    'material_delete'  => [
                        'name'       => '删除素材',
                        'url'        => 'plugin.material-center.admin.material.delete',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'material_delete',
                        'parents'    => ['material_center', 'material_list'],
                    ],
                    'edit_status'  => [
                        'name'       => '修改显示状态',
                        'url'        => 'plugin.material-center.admin.material.change_status',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'edit_status',
                        'parents'    => ['material_center', 'material_list'],
                    ],
                    'material_material_center'  => [
                        'name'       => '搜索商品',
                        'url'        => 'plugin.material-center.admin.material.getSearchGoods',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'material_material_center',
                        'parents'    => ['material_center', 'material_list'],
                    ],
                    'upload_files'  => [
                        'name'       => '上传图片',
                        'url'        => 'plugin.material-center.admin.material.upload_files',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'upload_files',
                        'parents'    => ['material_center', 'material_list'],
                    ],
                ],
            ],

            'material_set'     => [
                'name'       => '素材设置',
                'url'        => 'plugin.material-center.admin.set.index',
                'url_params' => '',
                'permit'     => 1,
                'menu'       => 1,
//                'icon'       => '',
                'sort'       => 0,
                'item'       => 'material_set',
                'parents'    => ['material_center'],
                'child'      => [
                    'share_set'  => [
                        'name'       => '素材分享',
                        'url'        => 'plugin.material-center.admin.set.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'share_set',
                        'parents'    => ['material_center', 'material_set'],
                    ],
                ]
            ]
        ],
    ]);
};