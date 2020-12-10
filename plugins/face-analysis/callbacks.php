<?php

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate', ['--path' => 'plugins/face-analysis/migrations', '--force' => true]);
    },
    app\common\events\PluginWasDeleted::class => function ($plugins) {
        \Artisan::call('migrate:rollback', ['--path' => 'plugins/face-analysis/migrations']);
    },
];