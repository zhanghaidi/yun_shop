<?php
 
return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate',['--path'=>'plugins/article/migrations','--force'=>true]);
    },
    app\common\events\PluginWasDisabled::class => function ($plugin) {


    },
    app\common\events\PluginWasDeleted::class => function () {
        \Artisan::call('migrate:rollback',['--path'=>'plugins/article/migrations']);
    }
];
