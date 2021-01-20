<?php

use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\GoodsPackage\common\config\MenuHook;

// 商品套餐插件的ID
define('PLUGIN_ID', 35);

return function (Dispatcher $events) {

    // 设置商品套餐ID
    \Config::set('plugins.goods_package.id', PLUGIN_ID);

    // 设置菜单
    \Config::set('plugins_menu.goods_package', MenuHook::menu());

    config()->push('shop-foundation.order-discount',[
        'key'=>'packageDiscount',
        'class'=>function (\app\frontend\modules\order\models\PreOrder $preOrder) {
            return new \Yunshop\GoodsPackage\common\discount\PackageDiscount($preOrder);
        }
    ]);
};
