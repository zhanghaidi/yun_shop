<?php

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate', ['--path' => 'plugins/examination/migrations', '--force' => true]);
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback', ['--path' => 'plugins/examination/migrations']);
    },
];