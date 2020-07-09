<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 上午11:08
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Controllers;


use app\common\components\ApiController;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Frontend\Modules\Love\Models\LoveActivationRecords;

class ActivationRecordsController extends ApiController
{
    const PAGE_SIZE = 10;

    public function index()
    {
        if (!$this->getMemberModel()) {
            return $this->errorJson('未获取到会员信息');
        }

        return $this->successJson('ok',$this->getRecords());

    }

    /**
     * 获取会员爱心值激活记录
     * @return array
     */
    private function getRecords()
    {
        $records = LoveActivationRecords::records()->orderBy('created_at','desc')->paginate(static::PAGE_SIZE,'','',$this->getPostPage());
        return $records ? $records->toArray() : [];
    }

    private function getPostPage()
    {
        return \YunShop::request()->page ?: 1;
    }

    /**
     * 获取会员 model 实例
     * @return mixed
     */
    private function getMemberModel()
    {
        return CommonService::getMemberModel($this->getMemberId());
    }

    /**
     * 获取登陆会员ID值
     * @return int
     */
    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }

}
