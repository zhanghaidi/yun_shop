<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/1
 * Time: 上午10:26
 */

namespace Yunshop\RechargeCode\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\services\ExportService;
use Yunshop\RechargeCode\common\models\RechargeCode;
use Yunshop\RechargeCode\common\services\QrCode;

class SetController extends BaseController
{

    public function index()
    {

        $setting = \Setting::get('plugin.recharge-code');
        if (\Request::getMethod() == 'POST') {
            $data = \YunShop::request()->setting;
            if($data){
                if (\Setting::set('plugin.recharge-code', $data)) {
                    return $this->message('设置成功', Url::absoluteWeb('plugin.recharge-code.admin.set.index'));
                } else {
                    return $this->error('设置失败');
                }
            }
        }
        return view('Yunshop\RechargeCode::admin.set', [
            'setting' => $setting
        ])->render();

    }

}