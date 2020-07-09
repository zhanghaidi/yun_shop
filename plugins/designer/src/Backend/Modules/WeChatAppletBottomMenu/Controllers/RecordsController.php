<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-05
 * Time: 15:16
 */

namespace Yunshop\Designer\Backend\Modules\WeChatAppletBottomMenu\Controllers;


class RecordsController extends \Yunshop\Designer\Backend\Modules\BottomMenu\Controllers\RecordsController
{
    protected $ingress = 'weChatApplet';

    /**a
     * @var string
     */
    protected $storeUrl = 'plugin.designer.Backend.Modules.WeChatAppletBottomMenu.Controllers.store.index';

    /**
     * @var string
     */
    protected $defaultUrl = 'plugin.designer.Backend.Modules.WeChatAppletBottomMenu.Controllers.default.index';

    /**
     * @var string
     */
    protected $destroyUrl = 'plugin.designer.Backend.Modules.WeChatAppletBottomMenu.Controllers.destroy.index';
}
