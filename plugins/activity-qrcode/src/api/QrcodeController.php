<?php

namespace Yunshop\ActivityQrcode\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Illuminate\Support\Facades\DB;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午2:29
 */
class QrcodeController extends ApiController
{
    protected $publicController = ['Qrcode'];
    protected $publicAction = ['index', 'scan'];
    protected $ignoreAction = ['index', 'scan'];

    //活码维码展示页面
    public function index()
    {
        return $this->successJson('ok-index');

    }

   //扫码识别页面
    public function scan()
    {
        return $this->successJson('ok-scan');
    }

}