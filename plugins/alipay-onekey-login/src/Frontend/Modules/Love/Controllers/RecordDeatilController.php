<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/7 下午5:01
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Controllers;


use app\common\components\ApiController;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Frontend\Modules\Love\Models\LoveRecords;

class RecordDeatilController extends ApiController
{
    private $recordModel;

    public function index()
    {
        if (!$this->getMemberModel()) {
            return $this->errorJson('未获取到会员信息');
        }
        if ($this->getRecordModel()) {
            return $this->errorJson('未获取到记录信息');
        }
        return $this->successJson('ok',$this->getRecordDetail());

    }

    private function getRecordDetail()
    {

    }

    private function getRecordDetailByType()
    {
        //$record = LoveRecords::
        switch ($this->recordModel->type) {
            case ConstService::SOURCE_TRANSFER:

        }
    }

    private function getRecordModel()
    {
        return $this->recordModel = LoveRecords::ofRecordId($this->getPostRecordId())->first();
    }

    private function getPostRecordId()
    {
        return trim(\YunShop::request()->id);
    }

    private function getMemberModel()
    {
        return CommonService::getMemberModel($this->getMemberId());
    }

    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }


}
