<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-05
 * Time: 16:23
 */

namespace Yunshop\Designer\Backend\Modules\WeChatAppletBottomMenu\Controllers;


class StoreController extends \Yunshop\Designer\Backend\Modules\BottomMenu\Controllers\StoreController
{
    /**
     * 终端类型
     *
     * @var string
     */
    protected $ingress = 'weChatApplet';

    /**
     * 保存后跳转URL
     *
     * @var string
     */
    protected $jumpUrl = 'plugin.designer.Backend.Modules.WeChatAppletBottomMenu.Controllers.records.index';

    /**
     * 提交数据URL
     *
     * @var string
     */
    protected $storeUrl = 'plugin.designer.Backend.Modules.WeChatAppletBottomMenu.Controllers.store.update';

}
