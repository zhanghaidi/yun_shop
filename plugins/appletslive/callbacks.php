<?php
/**
 * Created by PhpStorm.
 * User: CSY
 * Date: 2019/10/18
 * Time: 10:01
 */
return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate',['--path'=>'plugins/appletslive/migrations','--force'=>true]);
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback',['--path'=>'plugins/appletslive/migrations']);
    }

];