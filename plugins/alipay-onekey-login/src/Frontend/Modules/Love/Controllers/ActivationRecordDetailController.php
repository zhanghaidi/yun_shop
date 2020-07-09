<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/13 上午10:35
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Controllers;


use app\common\components\ApiController;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Frontend\Modules\Love\Models\LoveActivationRecords;

class ActivationRecordDetailController extends ApiController
{
    private $_model;


    /**
     * 激活记录详情接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (!$this->getMemberModel()) {
            return $this->errorJson('未获取到会员信息');
        }
        if (!$this->getPostRecordId()) {
            return $this->errorJson('未获取到记录ID');
        }
        if (!$this->getRecordModel()) {
            return $this->errorJson('未获取到记录信息');
        }

        return $this->successJson('ok',$this->getRecordDetail());
    }

    /**
     * 激活记录详情数据
     * @return array
     */
    private function getRecordDetail()
    {
        return [
            'fixed_activation'          => [
                "fixed_proportion"          => $this->_model->fixed_proportion,
                "fixed_activation_love"     => $this->_model->fixed_activation_love,
                "member_froze_love"         => $this->_model->member_froze_love
            ],
            'first_commission'          => [
                'order_money'               => $this->_model->first_order_money,
                'proportion'                => $this->_model->first_proportion,
                'activation_love'           => $this->_model->first_activation_love,
            ],
            'second_three_Commission'   =>[
                'order_money'               => $this->_model->second_three_order_money,
                'proportion'                => $this->_model->second_three_proportion,
                'team_leve_award'           => $this->_model->last_upgrade_team_leve_award,
                'fetter_proportion'         => $this->_model->second_three_fetter_proportion,

                //'activation_love'         => $this->_model->second_three_activation_love,
                //'sum_activation_love'     => $this->_model->sum_activation_love,
                'activation_love'           => $this->_model->second_three_activation_love,
            ],
            'other'                     =>[
                'id'                        => $this->_model->id,
                'sum_activation_love'       => $this->_model->sum_activation_love,
                'actual_activation_love'    => $this->_model->actual_activation_love,
                'created_at'                => $this->_model->created_at->toDateTimeString(),
                'order_sn'                  => $this->_model->order_sn
            ]


        ];
    }

    /**
     * 激活记录 model 实例
     * @return mixed
     */
    private function getRecordModel()
    {
        return $this->_model = LoveActivationRecords::ofId($this->getPostRecordId())->first();
    }

    /**
     * 获取提交的记录ID值
     * @return string
     */
    private function getPostRecordId()
    {
        return trim(\YunShop::request()->record_id);
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
