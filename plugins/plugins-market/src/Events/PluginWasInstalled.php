<?php

namespace Yunshop\PluginsMarket\Events;

use app\common\events\Event;
use app\common\services\Plugin;

class PluginWasInstalled extends Event
{
    public $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
}
