<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-05-13
 * Time: 16:09
 */

namespace Yunshop\Love\Backend\Controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;

class DividendSetController extends BaseController
{
    public function index()
    {
        $love = \Yunshop\Love\Common\Services\SetService::getLoveName();

        $set = Setting::get('plugin.love_dividend');
        $requestModel = \YunShop::request()->setdata;
        if ($requestModel) {
            if (Setting::set('plugin.love_dividend', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.love.Backend.Controllers.dividend-set'));
            }
            return $this->error('设置失败');
        }

        return view('Yunshop\Love::Backend.dividendSet', [
            'set'  => $set,
            'love' => $love,
        ])->render();
    }
}
