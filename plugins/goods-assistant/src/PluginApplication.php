<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\GoodsAssistant;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }
    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('goods-assistant',[
            'name' => '商品助手',
            'type' => 'tool',
            'url' => 'plugin.goods-assistant.admin.import.taobao',// url 可以填写http 也可以直接写路由
            'urlParams' => '',//如果是url填写的是路由则启用参数否则不启用
            'permit' => 1,//如果不设置则不会做权限检测
            'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
            'top_show'    => 0,
            'left_first_show'   => 0,
            'left_second_show'   => 1,
            'icon' => 'fa-circle-o-notch',//菜单图标
            'list_icon' => 'goods_assistant',
            'parents'=>[],
            'child' => [
                'yzGoods' => [
                    'name' => '商品导入',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.goods-assistant.admin.import-yz.yz-goods',
                    'urlParams' => [],
                    'parents'=>['goods-assistant'],
                ],
                'taobao' => [
                    'name' => '淘宝商品导入',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.goods-assistant.admin.import.taobao',
                    'urlParams' => [],
                    'parents'=>['goods-assistant'],
                ],
                /*'jingdong' => [
                    'name' => '京东商品导入',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.goods-assistant.admin.import.jingdong',
                    'urlParams' => [],
                    'parents'=>['goods-assistant'],
                ],*/
                'taobaoCSV'     => [
                    'name'      => 'CSV上传',
                    'permit'    => 1,
                    'menu'      => 1,
                    'icon'      => '',
                    'url'       => 'plugin.goods-assistant.admin.importTaobaoCSV.taobaoCSV',
                    'urlParams' => [],
                    'parents'   => ['goods-assistant'],
                ],
                'uniacidImport' => [
                    'name' => '公众号商品导入',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.goods-assistant.admin.ImportUniacid.index',
                    'urlParams' => [],
                    'parents' => ['goods-assistant'],
                ]
            ]
        ]);

    }

    public function boot()
    {


    }
    public function register()
    {
        //    define('TAOBAOINFO', "http://hws.m.taobao.com/cache/wdetail/5.0/?id=");
        define('TAOBAOINFO', "https://detail.tmall.com/item.htm?id=");
        define('JDINFO', 'http://item.m.jd.com/ware/view.action?wareId=');
        define('ALIINFO', 'https://detail.1688.com/offer/');
        define('TAOBAODETAIL', 'http://hws.m.taobao.com/cache/wdesc/5.0/?id=');
        define('JDDETAIL', 'http://item.m.jd.com/ware/detail.json?wareId=');
//    define('YZINFO', 'http://dev-yanglei.yunzshop.com/addons/yun_shop/api.php?&route=import-goods.get-goods&goods_id=');
        define('YZINFO', 'https://www.yunzmall.com/addons/yun_shop/api.php?&route=import-goods.get-goods&goods_id=');
    }
}