<?php

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate', ['--path' => 'plugins/alipay-onekey-login/migrations', '--force' => true]);
        Log::info('[lease-toy] 插件已启用');

    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback', ['--path' => 'plugins/alipay-onekey-login/migrations']);
        Log::info('[lease-toy] 插件已禁用');

    },
];