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

class TradingSetController extends BaseController
{
    /**
     * 查看设置
     */
    public function see()
    {
        $set = Setting::get('plugin.love_trading');

        $requestModel = \YunShop::request()->setdata;
        if ($requestModel) {
            if (Setting::set('plugin.love_trading', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.love.Backend.Controllers.trading-set.see'));
            }
            return $this->error('设置失败');
        }
        return view('Yunshop\Love::Backend.tradingSet', [
            'set' => $set,
        ])->render();
    }

}
