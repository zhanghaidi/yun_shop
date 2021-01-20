<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2018/12/4
 * Time: 下午2:56
 */

use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events) {
    $set = \Setting::get('plugin.tbk');
    $set['name'] = $set['name'] ?: '淘宝客';


    \Config::set('plugins_menu.tbk',[
        'name' => $set['name'],
        'type' => 'tool',
        'url' => 'plugin.tbk.admin.set.index',// url 可以填写http 也可以直接写路由
        'urlParams' => '',//如果是url填写的是路由则启用参数否则不启用
        'permit' => 1,//如果不设置则不会做权限检测
        'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
        'icon' => 'fa-credit-card',//菜单图标
        'top_show'    => 0,
        'left_first_show'   => 0,
        'left_second_show'   => 1,
        'list_icon' => 'mryt',
        'parents'=>[],
        'child' => [
            'tbk.set' => [
                'name' => '淘宝客设置',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.tbk.admin.set.index',
                'urlParams' => [],
                'parents'=>['tbk'],
                'child' => []
            ],
            'tbk.order' => [
                'name' => '订单上传',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.tbk.admin.order.import',
                'urlParams' => [],
                'parents'=>['tbk'],
                'child' => []
            ],
            'tbk.goods' => [
                'name' => '商品管理',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.tbk.admin.goods.index',
                'urlParams' => [],
                'parents'=>['tbk'],
                'child' => []
            ],
            /*'tbk.copuon' => [
                'name' => '好券清单',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.tbk.admin.selection.coupon',
                'urlParams' => [],
                'parents'=>['tbk'],
                'child' => []
            ],*/
            'tbk.fav' => [
                'name' => '选品库',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.tbk.admin.selection.favList',
                'urlParams' => [],
                'parents'=>['tbk'],
                'child' => []
            ],
            /*'tbk.pid' => [
                'name' => '推广位',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.tbk.admin.selection.pid',
                'urlParams' => [],
                'parents'=>['tbk'],
                'child' => []
            ],*/
            /*'tbk.cron' => [
                'name' => '订单列表',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.tbk.admin.order.index',
                'urlParams' => [],
                'parents'=>['tbk'],
                'child' => []
            ],*/

        ]
    ]);

    $events->subscribe(\Yunshop\Tbk\common\listeners\RegMemberListener::class);


    \Event::listen('cron.collectJobs', function () {
        \Cron::add('Tbk-handleOrder', '*/10 * * * * *', function () {
            (new \Yunshop\Tbk\common\services\OrderDispatchService())->handle();
            return;
        });
    });

    /**
     * 商品的关联模型
     */
    app('GoodsManager')->bind('TbkGoods',function(){
        return new \Yunshop\Tbk\Frontend\Models\TbkGoods();
    });
    app('GoodsManager')->tag('TbkGoods','GoodsRelations');

    //$events->subscribe(\Yunshop\Printer\common\listeners\OrderCreatedListener::class);

    //$events->subscribe(\Yunshop\Printer\common\listeners\OrderPaidListener::class);

};