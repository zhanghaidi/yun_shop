<?php

use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events) {
    // 插件菜单
    require_once 'menu.php';
    // 消息通知
    require_once 'message.php';
    // 收入
    require_once 'income.php';

    // 监听者
    $events->subscribe(\Yunshop\Nominate\listeners\MemberLevelListener::class);

    $events->subscribe(\Yunshop\Nominate\listeners\OrderCreatedListener::class);

    $events->subscribe(\Yunshop\Nominate\listeners\OrderReceiveListener::class);

    $events->subscribe(\Yunshop\Nominate\listeners\OrderCloseListener::class);

    $events->subscribe(\Yunshop\Nominate\listeners\OrderCanceledListener::class);

    $events->subscribe(\Yunshop\Nominate\listeners\OrderRefundedListener::class);
};
