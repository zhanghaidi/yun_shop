<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/25
 * Time: 下午17:08
 */

return [
    app\common\events\PluginWasEnabled::class => function ($plugins) {
        \Artisan::call('migrate',['--path'=>'plugins/material-center/migrations','--force'=>true]);
    }
];