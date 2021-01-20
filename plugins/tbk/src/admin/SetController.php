<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/23
 * Time: 下午3:55
 */

namespace Yunshop\Tbk\admin;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\facades\Setting;

class SetController extends BaseController
{

    public function __construct()
    {

    }

    public function index()
    {
        //dd(11);
        $set = Setting::get('plugin.tbk');

        $requestModel = \YunShop::request()->setdata;

        if ($requestModel) {
            if (Setting::set('plugin.tbk', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.tbk.admin.set'));
            } else {
                $this->error('设置失败');
            }
        }

        return view('Yunshop\Tbk::admin.set', [
            'set' => $set
        ])->render();

    }
}