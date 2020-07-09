<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 上午9:38
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use Yunshop\Love\Common\Services\SetService;

class ReturnSetController extends BaseController
{
    public function index()
    {
        $love = \Yunshop\Love\Common\Services\SetService::getLoveName();

        $set = Setting::get('plugin.love_return');
        $requestModel = \YunShop::request()->setdata;
        if ($requestModel) {
            if (Setting::set('plugin.love_return', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.love.Backend.Controllers.return-set'));
            }
            return $this->error('设置失败');
        }
        for ($i = 0; $i <= 23; $i++) {
            $hourData[$i] = [
                'key'  => $i,
                'name' => $i . ":00",
            ];
        }

        return view('Yunshop\Love::Backend.returnSet', [
            'set'      => $set,
            'love'     => $love,
            'hourData' => $hourData,
        ])->render();
    }

}
