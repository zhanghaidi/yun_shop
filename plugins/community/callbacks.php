<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/23
 * Time: 下午17:25
 */

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate',['--path'=>'plugins/community/migrations','--force'=>true]);
    }
];