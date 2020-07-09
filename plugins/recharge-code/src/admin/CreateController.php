<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/1
 * Time: 上午10:26
 */

namespace Yunshop\RechargeCode\admin;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\RechargeCode\common\models\RechargeCode;

class CreateController extends BaseController
{
    /**
     * @name 批量生成充值码
     * @author
     * @return mixed|string
     */
    public function index()
    {

        if (request()->isMethod('post')) {
            RechargeCode::insert(RechargeCode::setRechargeCodes(request()->code));
            return $this->message(request()->code['total'] . '个充值码生成成功！', Url::absoluteWeb('plugin.recharge-code.admin.list.index'));
        }
        return view('Yunshop\RechargeCode::admin.create', [
            'love_name' => trans('Yunshop\Love::love.name')
        ])->render();
    }
}