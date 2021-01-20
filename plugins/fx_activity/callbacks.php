<?php

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate', ['--path' => 'plugins/fx_activity/migrations', '--force' => true]);
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback', ['--path' => 'plugins/fx_activity/migrations']);
    },
];