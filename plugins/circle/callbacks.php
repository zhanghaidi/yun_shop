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
        \Artisan::call('migrate',['--path'=>'plugins/circle/migrations','--force'=>true]);
        Log::info('[circle] 插件已启用');
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback',['--path'=>'plugins/circle/migrations']);
        Log::info('[circle] 插件已禁用');
    }

];
