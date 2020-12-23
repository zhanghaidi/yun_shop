<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/5 下午1:58
 * Email: livsyitian@163.com
 */
return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate',['--path'=>'plugins/xiaoe-clock/migrations','--force'=>true]);
        Log::info('[xiaoe-clock] 插件已启用');
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback',['--path'=>'plugins/xiaoe-clock/migrations']);
        Log::info('[xiaoe-clock] 插件已禁用');
    }

];
