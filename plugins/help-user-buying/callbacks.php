<?php
 
return [
    app\common\events\PluginWasEnabled::class => function (app\common\services\PluginManager $manager, $plugins) {
        // 你也可以在回调函数的参数列表中使用类型提示，Laravel 服务容器将会自动进行依赖注入
        \Artisan::call('migrate', ['--path' => 'plugins/help-user-buying/migrations', '--force' => true]);
        Log::info('[ExamplePlugin] 示例插件已启用，IoC 容器自动为我注入了 PluginManager 实例：', compact('manager'));
    },
    app\common\events\PluginWasDisabled::class => function ($plugin) {
        // 回调函数被调用时 Plugin 实例会被传入作为参数
        Log::info('[help-user-buying] 示例插件已禁用，我拿到了插件实例', ['instance' => $plugin]);
    },
    app\common\events\PluginWasDeleted::class => function () {
        \Artisan::call('migrate:rollback', ['--path' => 'plugins/help-user-buying/migrations']);
        Log::info('[help-user-buying] 啊啊啊啊啊啊啊我被删除啦 QwQ');
    }
];
