<?php

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate',['--path'=>'plugins/lucky-draw/migrations','--force'=>true]);
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback',['--path'=>'plugins/lucky-draw/migrations']);
    },
];
