<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 下午12:04
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Controllers;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\Love\Frontend\Modules\Love\Models\LoveTransferRecords;
use Yunshop\TeamDividend\models\TeamDividendAgencyModel;
use app\common\models\member\MemberChildren;

class TransferController extends ApiController
{
    private $memberModel;

    private $_model;


    /**
     * 爱心值转让接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        DB::beginTransaction();
        $this->validatorData();
        $result = $this->transfer();

        $result = $result === true ? $this->updateTransferStatus() : false;
        if ($result !== true) {
            DB::rollBack();
            return $this->errorJson('转让失败，请重试');
        }
        DB::commit();
        return $this->successJson('转让成功');
    }

    /**
     * 爱心值转让开始
     * @return bool
     */
    private function transfer()
    {
        return $this->transferRecordSave() === true ? $this->updateMemberLove() : false;
    }

    /**
     * 转让记录保存
     * @return bool
     * @throws AppException
     */
    private function transferRecordSave()
    {
        $this->_model = new LoveTransferRecords();

        $this->_model->fill($this->getTransferData());
        $validator = $this->_model->validator();
        if ($validator->fails()) {
            throw new AppException('转让数据有误');
        }
        if (!$this->_model->save()) {
            throw new AppException('转让数据写入失败');
        }
        return true;
    }

    /**
     * 修改转让状态
     * @return mixed
     */
    private function updateTransferStatus()
    {
        $this->_model->status = ConstService::STATUS_SUCCESS;
        return $this->_model->save();
    }

    /**
     * 修改会员爱心值
     * @return bool
     */
    private function updateMemberLove()
    {
        $_LoveChangeService = new LoveChangeService('usable');

        $result = $_LoveChangeService->transfer($this->loveTransferChangeData());
        if ($result !== true) {
            return $result;
        }
        $result = $_LoveChangeService->recipient($this->loveRecipientChangeData());
        if ($result !== true) {
            return $result;
        }
        return true;
    }

    /**
     * 验证数据
     * @return bool
     * @throws AppException
     */
    private function validatorData()
    {
        $love_name = $this->getLoveName();

        if (!$this->getTransferStatus()) {
            throw new AppException('未开启转让功能');
        }
        if (!$this->getMemberModel()) {
            throw new AppException('未获取到会员信息，请刷新重试');
        }
        if (bccomp($this->getPostChangeValue(),0,2) != 1) {
            throw new AppException('转让' . $love_name . '不能小于0');
        }


        $result = $this->getTransferFetter();
        if ($result && bccomp($this->getPostChangeValue(),$result,2) == -1) {
            throw new AppException('转让'.$love_name.'不能小于'.$result);
        }
        $result = $this->getTransferMultiple();
        if ($result && fmod($this->getPostChangeValue(),$result) != 0) {
            throw new AppException('转让'.$love_name.'必须是'.$result. '的倍数');
        }


        if ($this->getMemberId() == $this->getPostRecipient()) {
            throw new AppException('转让者不能是自己');
        }
        if (!$this->getRecipientModel()) {
            throw new AppException('被转让者不存在');
        }
        if (bccomp($this->getMemberLove(), $this->getPostChangeValue(),2) == -1) {
            throw new AppException('转让' . $love_name . '不能大于您的剩余' . $love_name);
        }


        if(app('plugins')->isEnabled('team-dividend') && SetService::getLoveSet('t_d_transfer') == 1) {
            // 转让者 id
            $getMemberId = $this->getMemberId();
            // 被转让者 id
            $getPostRecipient = $this->getPostRecipient();
            // 当前公众号 id
            $uniacid = \YunShop::app()->uniacid;
            $flag = $this->isTeamDividend($getMemberId, $getPostRecipient, $uniacid);
            if ($flag) {
                $this->isTeamDividendLevel($getMemberId, $getPostRecipient, $uniacid);
            }

        }

        return true;
    }


    /**
     * 判断登录用户转账的是否为下级且是否为经销商(根据不同的结果返回不同的结果)
     *
     * @param $memberId
     * @param $recipientId
     * @param $uniacid
     * @return bool
     * @throws AppException
     */
    private function isTeamDividend($memberId, $recipientId, $uniacid)
    {
        // flag = true 表示该下级是经销商 反得之 flag = false
        $flag = true;
        $children = MemberChildren::where('member_id', $memberId)->where('child_id', $recipientId)->where( 'uniacid', $uniacid)->get();

        // 判断该下级是否为经销商
        $is_team_dividend = TeamDividendAgencyModel::where('uid', $recipientId)->first();
        if (empty($is_team_dividend)) {
            $flag = false;
        }
        if (empty($children[0])) {
            throw new AppException('转让的用户不是您的下级经销商');
        }
        return $flag;
    }

    /**
     * 判断登录用户的经销商等级是否等于被转账者的经销商等级
     *
     */
    private function isTeamDividendLevel($getMemberId, $getPostRecipient, $uniacid)
    {
        $getMemberLevel = TeamDividendAgencyModel::with('hasOneLevel')->whereIn('uid', [$getMemberId, $getPostRecipient])->where( 'uniacid', $uniacid)->get();

        if ($getMemberLevel[0]->hasOneLevel['level_weight'] == $getMemberLevel[1]->hasOneLevel['level_weight']) {
            throw new AppException('转让的经销商等级等于被转让转账者经销商等级');
        }
        return true;
    }


    /**
     * 转让记录 data 数组
     * @return array
     */
    private function getTransferData()
    {
        return [
            'uniacid'           => \YunShop::app()->uniacid,
            'transfer'          => $this->getMemberId(),
            'recipient'         => $this->getPostRecipient(),
            'change_value'      => $this->getPostChangeValue(),
            'status'            => ConstService::STATUS_FAILURE,
            'order_sn'          => $this->getOrderSN(),
            'poundage'          => $this->getTransferPoundage(),
            'proportion'        => $this->getTransferPoundageProportion(),
        ];
    }

    /**
     * 爱心值明细 转让--转出 data 数组
     * @return array
     */
    private function loveTransferChangeData()
    {
        return [
            'member_id'         => $this->memberModel->uid,
            'change_value'      => $this->getPostChangeValue(),
            'operator'          => ConstService::OPERATOR_MEMBER,
            'operator_id'       => $this->memberModel->uid,
            'remark'            => $this->getLoveChangeRemark(),
            'relation'          => $this->_model->order_sn
        ];
    }

    /**
     * 爱心值明细 转让--转入 data 数组
     * @return array
     */
    private function loveRecipientChangeData()
    {
        return [
            'member_id'         => $this->getPostRecipient(),
            'change_value'      => $this->getActualTransferValue(),
            'operator'          => ConstService::OPERATOR_MEMBER,
            'operator_id'       => $this->memberModel->uid,
            'remark'            => $this->getLoveChangeRemark(),
            'relation'          => $this->_model->order_sn
        ];
    }

    /**
     * 爱心值明细 转让  备注
     * @return string
     */
    private function getLoveChangeRemark()
    {
        return '会员ID' . $this->memberModel->uid . '转让会员ID' . $this->getPostRecipient() . $this->getLoveName() . $this->getPostChangeValue() . '扣除手续费为' . $this->getActualTransferValue();
    }

    /**
     * 扣除手续费 被转让者实际接受转让的爱心值
     * @return string
     */
    private function getActualTransferValue()
    {
        return bcsub($this->getPostChangeValue(),$this->getTransferPoundage(),2);
    }

    /**
     * 获取爱心值自定义名称
     * @return mixed|string
     */
    private function getLoveName()
    {
        return CommonService::getLoveName();
    }

    /**
     * 转让手续费
     * @return string
     */
    private function getTransferPoundage()
    {
        return bcdiv(bcmul($this->getPostChangeValue(),$this->getTransferPoundageProportion(),2),100,2);
    }

    /**
     * 转染手续费比例
     * @return string
     */
    private function getTransferPoundageProportion()
    {
        return SetService::getTransferPoundageProportion();
    }

    /**
     * 转让限制：转让最小额度
     * @return string
     */
    private function getTransferFetter()
    {
        return SetService::getTransferFetter();
    }

    /**
     *  转让限制：转让倍数
     * @return string
     */
    private function getTransferMultiple()
    {
        return SetService::getTransferMultiple();
    }

    /**
     * 生成订单号
     * @return string
     */
    private function getOrderSN()
    {
        $ordersn = createNo('TL', true);
        while (1) {
            if (!LoveTransferRecords::ofOrderSn($ordersn)->first()) {
                break;
            }
            $ordersn = createNo('TL', true);
        }
        return $ordersn;
    }

    /**
     * 转让功能开关
     * @return bool
     */
    private function getTransferStatus()
    {
        return SetService::getTransferStatus();
    }

    /**
     * 获取提交的转让值
     * @return string
     */
    private function getPostChangeValue()
    {
        return trim(\YunShop::request()->change_value) ?: '0';
    }

    /**
     * 获取提交的转让者
     * @return string
     */
    private function getPostRecipient()
    {
        return trim(\YunShop::request()->recipient);
    }

    /**
     * 获取转让者 model 实例
     * @return mixed
     */
    private function getRecipientModel()
    {
        return CommonService::getMemberModel($this->getPostRecipient());
    }

    private function getMemberLove()
    {
        return isset($this->memberModel->love->usable) ? $this->memberModel->love->usable : "0";
    }

    /**
     * 获取登陆会员的model实例
     * @return mixed
     */
    private function getMemberModel()
    {
        return $this->memberModel = CommonService::getLoveMemberModelById($this->getMemberId());
    }

    /**
     * 获取登陆会员的ID
     * @return int
     */
    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }
}
