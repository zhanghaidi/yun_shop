<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Poster;


use app\common\services\Hook;
use Yunshop\Poster\Listener\WechatMessageListener;
use Yunshop\Poster\Listener\WechatProcessorListener;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {

    }

    protected function setMenuConfig()
    {
        //menu
        \app\backend\modules\menu\Menu::current()->setPluginMenu('poster', [
            'name' => '海报',
            'type' => 'marketing',
            'url' => 'plugin.poster.admin.poster.index',
            'url_params' => '',
            'permit' => 1,
            'menu' => 1,
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => 'fa-picture-o',
            'list_icon' => 'poster',
            'parents' => [],
            'child' => [
                'admin_poster_index' => [
                    'name' => '海报管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-th-list',
                    'url' => 'plugin.poster.admin.poster.index',
                    'url_params' => '',
                    'parents' => ['poster'],
                    'child' => [
                        'admin_poster_record' => [
                            'name'          => '海报生成记录',
                            'permit'        => 1,
                            'menu'          => 0,
                            'icon'          => '',
                            'url'           => 'plugin.poster.admin.poster-record.index',
                            'url_params'    => '',
                            'parents'       => ['poster','admin_poster_index'],
                        ],
                        'admin_poster_list' => [
                            'name' => '浏览列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.poster.admin.poster.index',
                            'url_params' => '',
                            'parents' => ['poster', 'admin_poster_index'],
                        ],
                        'admin_poster_add' => [
                            'name' => '新增海报',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.poster.admin.poster.add',
                            'url_params' => '',
                            'parents' => ['poster', 'admin_poster_index'],
                        ],
                        'admin_poster_edit' => [
                            'name' => '编辑海报',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.poster.admin.poster.edit',
                            'url_params' => '',
                            'parents' => ['poster', 'admin_poster_index'],
                        ],
                        'admin_poster_destroy' => [
                            'name' => '删除海报',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.poster.admin.poster.delete',
                            'url_params' => '',
                            'parents' => ['poster', 'admin_poster_index'],
                        ],
                    ],
                ],

                'admin_poster_scan' => [
                    'name' => '扫码记录',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-qrcode',
                    'url' => 'plugin.poster.admin.poster-scan.index',
                    'url_params' => '',
                    'parents' => ['poster'],
                ],
                'admin_poster_award' => [
                    'name' => '奖励记录',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-bookmark',
                    'url' => 'plugin.poster.admin.poster-award.index',
                    'url_params' => '',
                    'parents' => ['poster'],
                ],
            ]
        ]);
    }

    public function boot()
    {
        $events = app('events');
        //CSS
        Hook::addStyleFileToPage(plugin_assets('poster', 'assets/css/daterangepicker.css'));

        //JavaScript
        Hook::addScriptFileToPage(plugin_assets('poster', 'assets/js/designer.js'));

        //listener 区分新旧框架，新框架走新监听，其他情况走原来的
        if (config('APP_Framework') == 'platform') {
            $events->subscribe(WechatMessageListener::class);
        } else {
            $events->subscribe(WechatProcessorListener::class);
        }

    }

}