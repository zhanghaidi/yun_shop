<?php

namespace Yunshop\WechatComplaint;

use Yunshop\WechatComplaint\services\WechatComplaintService;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu(WechatComplaintService::get(), [
            'name' => WechatComplaintService::get('name'),
            'type' => 'tool',
            'url' => 'plugin.wechat-complaint.admin.item.index', // url 可以填写http 也可以直接写路由
            'url_params' => '', //如果是url填写的是路由则启用参数否则不启用
            'permit' => 1, //如果不设置则不会做权限检测
            'menu' => 1, //如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '', //菜单图标
            'list_icon' => 'area_dividend',
            'parents' => [],
            'child' => [
                'item_manage' => [
                    'name' => '投诉项管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.wechat-complaint.admin.item.index',
                    'url_params' => '',
                    'parents' => ['wechat_complaint'],
                    'child' => [
                        'item_edit' => [
                            'name' => '编辑投诉项',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.wechat-complaint.admin.item.edit',
                            'parents' => ['wechat_complaint', 'item_manage'],
                        ],
                        'item_del' => [
                            'name' => '删除投诉项',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.wechat-complaint.admin.item.delete',
                            'parents' => ['wechat_complaint', 'item_manage'],
                        ],
                    ],
                ],

                'project_manage' => [
                    'name' => '投诉数据',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.wechat-complaint.admin.project.index',
                    'url_params' => '',
                    'parents' => ['wechat_complaint'],
                    'child' => [
                        'project_edit' => [
                            'name' => '编辑投诉来源',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.wechat-complaint.admin.project.edit',
                            'parents' => ['wechat_complaint', 'project_manage'],
                        ],
                        'project_del' => [
                            'name' => '删除投诉来源',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.wechat-complaint.admin.project.delete',
                            'parents' => ['wechat_complaint', 'project_manage'],
                        ],
                        'log_manage' => [
                            'name' => '投诉数据管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.wechat-complaint.admin.log.index',
                            'parents' => ['wechat_complaint', 'project_manage'],
                        ],
                        'log_delete' => [
                            'name' => '删除投诉数据',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.wechat-complaint.admin.log.delete',
                            'parents' => ['wechat_complaint', 'project_manage'],
                        ],
                    ],
                ],

            ],
        ]);
    }
}
