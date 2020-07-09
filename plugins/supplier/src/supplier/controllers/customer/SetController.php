<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/13
 * Time: 上午10:21
 */

namespace Yunshop\Supplier\supplier\controllers\customer;

use app\common\components\BaseController;
use app\common\helpers\Url;

class SetController extends BaseController
{
    public function index()
    {
        $set = \Setting::get('plugin.supplier.customer[' . \YunShop::app()->uid . ']');
        $set_data = request()->form_data;
        if ($set_data) {
            $set_data['uid'] = \YunShop::app()->uid;
            \Setting::set('plugin.supplier.customer[' . \YunShop::app()->uid . ']', $set_data);
            return $this->message('保存成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.customer.set.index'));
        }

        return view('Yunshop\Supplier::supplier.customer.set', [
            'set' => $set
        ]);
    }
}