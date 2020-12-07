<?php

namespace Yunshop\FaceAnalysis;

use Yunshop\FaceAnalysis\services\FaceAnalysisService;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('face_analysis', [
            'name' => (new FaceAnalysisService)->get('name'),
            'type' => 'marketing',
            'url' => 'plugin.face-analysis.admin.face-analysis-log-manage.index', // url 可以填写http 也可以直接写路由
            'url_params' => '', //如果是url填写的是路由则启用参数否则不启用
            'permit' => 1, //如果不设置则不会做权限检测
            'menu' => 1, //如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '', //菜单图标
            'list_icon' => 'face_payment',
            'parents' => [],
            'child' => [
                'face_analysis_manage' => [
                    'name' => '检测记录',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.face-analysis.admin.face-analysis-log-manage.index',
                    'url_params' => '',
                    'parents' => ['face_analysis'],
                    // 'child' => [
                    //     'add_help' => [
                    //         'name' => '添加帮助',
                    //         'permit' => 1,
                    //         'menu' => 0,
                    //         'url' => 'plugin.face-analysis.admin.help-center-add.index',
                    //         'parents' => ['face_analysis', 'face_analysis_manage'],
                    //     ],
                    // ]
                ],

                'face_analysis_ranking' => [
                    'name' => '排行榜',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.face-analysis.admin.face-beauty-ranking.index',
                    'url_params' => '',
                    'parents' => ['face_analysis'],
                ],

                'face_analysis_set' => [
                    'name' => '插件设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.face-analysis.admin.face-analysis-set.index',
                    'url_params' => '',
                    'parents' => ['face_analysis'],
                ],

            ]
        ]);
    }

    public function boot()
    {
    }
}
