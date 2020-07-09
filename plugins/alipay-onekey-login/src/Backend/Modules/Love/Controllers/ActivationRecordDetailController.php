<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/8/8 下午5:30
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Love\Controllers;


use app\common\components\BaseController;
use Yunshop\Love\Backend\Modules\Love\Models\LoveActivationRecords;

class ActivationRecordDetailController extends BaseController
{
    public function index()
    {
        return view('Yunshop\Love::Backend.Love.activationRecordDetail',[
            'detail' => $this->getRecordDetail()
        ])->render();
    }

    private function getRecordDetail()
    {
        return LoveActivationRecords::ofId($this->getPostRecordId())->records()->first();
    }

    private function getPostRecordId()
    {
        return \YunShop::request()->record_id;
    }

}
