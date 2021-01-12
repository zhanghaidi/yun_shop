<?php

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate', ['--path' => 'plugins/wechat-complaint/migrations', '--force' => true]);
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback', ['--path' => 'plugins/wechat-complaint/migrations']);
    },
];