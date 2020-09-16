<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/13 上午10:18
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Frontend\Controllers;


use app\common\components\ApiController;
use Yunshop\Sign\Common\Services\SetService;

class ShareController extends ApiController
{
    public function index()
    {
        $data = [
            'share_title'   => SetService::getSignSet('share_title'),
            'share_icon'    => yz_tomedia(SetService::getSignSet('share_icon')),
            'share_desc'    => SetService::getSignSet('share_desc')
        ];

        return $this->successJson('ok', ['share' => $data]);
    }

}
