<?php

return [
    \app\common\events\PluginWasEnabled::class => function (\app\common\services\PluginManager $manager) {
        //$manager->uninstall('plugins-market');
    }
];
