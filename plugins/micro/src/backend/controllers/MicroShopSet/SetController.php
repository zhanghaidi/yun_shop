<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/10
 * Time: 下午4:34
 */

namespace Yunshop\Micro\backend\controllers\MicroShopSet;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;
use app\common\services\MessageService;
use Setting;

class SetController extends BaseController
{
    private $url = 'plugin.micro.backend.controllers.MicroShopSet.set';

    public function index(\Request $request)
    {
        $set = Setting::get('plugin.micro');
        $set_data = \YunShop::request()->setdata;

        if ($set_data) {
            $is_save = true;
            if (!empty($set_data['cycle']) && !is_numeric($set_data['cycle'])) {
                $this->error('结算周期请填写整数');
                $is_save = false;
            }
            if ($is_save) {
                $set_data['micro_title'] = $set_data['micro_title']?$set_data['micro_title']:'微店';
                if (Setting::set('plugin.micro', $set_data)) {
                    return $this->message('设置成功', Url::absoluteWeb($this->url));
                } else {
                    $this->error('设置失败');
                }
            }
        }
        $set['fee'] = isset(Setting::get('withdraw.micro')['poundage_rate'])?Setting::get('withdraw.micro')['poundage_rate'].'%':'0.00%';

        $temp_list = MessageTemp::getList();

        return view('Yunshop\Micro::backend.MicroShopSet.set', [
            'set' => $set,
            'var' => \YunShop::app()->get(),
            'refund_days' => Setting::get('shop.trade')['refund_days'],
            'temp_list' => $temp_list
        ])->render();
    }
}