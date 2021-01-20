<?php
/****************************************************************
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate',['--path'=>'plugins/goods-package/migrations','--force'=>true]);
        Log::info('[goods-package] 插件已启用');
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback',['--path'=>'plugins/goods-package/migrations']);
        Log::info('[goods-package] 插件已禁用');
    }

];