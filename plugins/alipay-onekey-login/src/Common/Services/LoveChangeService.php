<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/28 上午11:51
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use app\common\exceptions\AppException;
use app\common\models\notice\MessageTemp;
use app\common\services\credit\Credit;
use app\common\services\MessageService;
use Yunshop\Diyform\admin\DiyformDataController;
use Yunshop\Love\Common\Models\LoveRecords;
use Yunshop\Love\Common\Models\Member;
use Yunshop\Love\Common\Models\MemberLove;

class LoveChangeService extends Credit
{
    protected $valueType;

    /**
     * relation 单号字段验证情况
     *
     * @var bool
     */
    protected $relationSituation;


    /*public function test()
    {
        return [
            'member_id'         => $this->memberModel->uid,
            'change_value'      => $this->getPostChangeValue(),
            'operator'          => ConstService::OPERATOR_MEMBER,
            'operator_id'       => $this->memberModel->uid,
            'remark'            => $this->getLoveChangeRemark(),
            'relation'          => $this->_model->order_sn
        ];
    }*/

    /**
     * LoveChangeService constructor.
     *
     * @param string $valueType usable|froze
     */
    public function __construct($valueType = '')
    {
        $this->valueType = $this->getValueType($valueType);
    }

    public function recharge(array $data)
    {
        return parent::recharge($data);
    }

    public function rechargeAwardFirstParent(array $data)
    {
        $this->source = ConstService::SOURCE_RECHARGE_AWARD_FIRST;
        return $this->addition($data);
    }

    public function rechargeAwardSecondParent(array $data)
    {
        $this->source = ConstService::SOURCE_RECHARGE_AWARD_SECOND;
        return $this->addition($data);
    }

    public function taskAward(array $data)
    {
        $this->source = ConstService::SOURCE_TASK_AWARD;
        return $this->addition($data);
    }

    /**
     * 充值消费积分赠送爱心值
     * @param array $data
     * @return string
     */
    public function integralAward(array $data)
    {
        $this->source = ConstService::SOURCE_AWARD;
        return $this->addition($data);
    }



    public function rechargeMinus(array $data)
    {
        return parent::rechargeMinus($data);
    }

    public function withdrawal(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        return parent::withdrawal($data);
    }

    //提现到消费积分
    public function withdrawalIntegral(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::Cash_Consumption_Points;
        return $this->subtraction($data);
    }

    public function pointTransfer(array $data)
    {
        $this->source = ConstService::SOURCE_POINT_TRANSFER;
        $this->valueType = ConstService::VALUE_TYPE_USABLE;

        return $this->addition($data);
    }

    public function signAward(array $data)
    {
        $this->source = ConstService::SOURCE_SIGN_AWARD;

        return $this->addition($data);
    }

    public function conver(array $data)
    {
        $this->source = ConstService::TRANSFORMATION_BALANCE;
        return $this->addition($data);
    }


    public function frozeAward(array $data)
    {
        $this->source = ConstService::SOURCE_FROZE_AWARD;
        $this->valueType = ConstService::VALUE_TYPE_USABLE;

        return $this->addition($data);
    }

    /**
     * 收入提现奖励：12
     * @param array $data
     * @return string
     */
    public function withdrawAward(array $data)
    {
        $this->source = ConstService::SOURCE_WITHDRAW_AWARD;
        return $this->addition($data);
    }

    /**
     * 直推奖奖励：13
     * @param array $data
     * @return string
     */
    public function directPushAward(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_FROZE;
        $this->source = ConstService::SOURCE_DIRECT_PUSH_AWARD;
        return $this->addition($data);
    }

    /**
     * 团队代理等级奖励：14
     * @param array $data
     * @return string
     */
    public function teamLevelAward(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_FROZE;
        $this->source = ConstService::SOURCE_TEAM_LEVEL_AWARD;
        return $this->addition($data);
    }

    /**
     * 购物上级获得：17
     * @param array $data
     * @return string
     */
    public function parentAward(array $data)
    {
        $this->source = ConstService::SOURCE_PARENT_AWARD;
        return $this->addition($data);
    }

    /**
     * 分销下线奖励：18
     * @param array $data
     * @return string
     */
    public function commissionAward(array $data)
    {
        $this->source = ConstService::SOURCE_COMMISSION_AWARD;
        return $this->addition($data);
    }

    /**
     * 交易撤回：19
     * @param array $data
     * @return string
     */
    public function revokeAward(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::SOURCE_REVOKE_AWARD;
        return $this->addition($data);
    }

    /**
     * 交易 收购：20
     * @param array $data
     * @return string
     */
    public function purchaseAward(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::SOURCE_PURCHASE_AWARD;
        return $this->addition($data);
    }

    /**
     * 出售 收购：21
     * @param array $data
     * @return string
     */
    public function sellAward(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::SOURCE_SELL_AWARD;
        return $this->subtraction($data);
    }

    /**
     * 返现：22
     * @param array $data
     * @return string
     */
    public function returnAward(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::SOURCE_RETURN_AWARD;
        return $this->subtraction($data);
    }

    /**
     * 转让--转出 3
     * @param array $data
     * @return string
     */
    public function transfer(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        return parent::transfer($data);
    }

    /**
     * 转让--转入 15
     * @param array $data
     * @return string
     */
    public function recipient(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::SOURCE_RECIPIENT;
        return $this->addition($data);
    }

    /**
     * 消费抵扣：4
     * @param array $data
     * @return string
     */
    public function deduction(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        return parent::deduction($data);
    }

    /**
     * 冻结爱心值激活 16
     * @param array $data
     * @return string
     * @throws AppException
     */
    public function activation(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_FROZE;
        $this->source = ConstService::SOURCE_ACTIVATION;
        $result = $this->subtraction($data);
        if ($result !== true) {
            throw new AppException('冻结变动修改失败');
        }

        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        return $this->addition($this->data);
    }

    /**
     * 加速激活
     * @param array $data
     * @return string
     * @throws AppException
     */
    public function quickenActivation(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_FROZE;
        $this->source = ConstService::SOURCE_QUICKEN_ACTIVATION;
        $result = $this->subtraction($data);//执行减法运算，修改数据库数据
        if ($result !== true) {
            throw new AppException('冻结变动修改失败');
        }

        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        return $this->addition($this->data);
    }

    /**
     * 经销商分红转入：28
     * @param array $data
     * @return string
     */
    public function teamDividendTransfer(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::SOURCE_TEAM_DIVIDEND;
        return $this->addition($data);
    }

    /**
     * 经销商获得爱心值（冻结）：28
     * @param array $data
     * @return string
     */
    public function teamDividendTransferFreeze(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_FROZE;
        $this->source = ConstService::SOURCE_TEAM_DIVIDEND;
        return $this->addition($data);
    }

    /**
     * 分销商等级上级赠送：29
     * @param array $data
     * @return string
     */
    public function commissionLevelGive(array $data)
    {
        $this->source = ConstService::SOURCE_COMMISSION_LEVEL_AWARD;
        return $this->addition($data);
    }

    /**
     * 名片新增会员奖励 30
     * @param array $data
     * @return bool|string
     */
    public function CardRegisterAward(array $data)
    {
        $this->source = ConstService::CARD_REGISTER_AWARD;
        return $this->addition($data);
    }

    /**
     * 名片访问奖励 31
     * @param array $data
     * @return bool|string
     */
    public function CardVisitAward(array $data)
    {
        $this->source = ConstService::CARD_VISIT_AWARD;
        return $this->addition($data);
    }

    /**
     * 配送站获得爱心值：32
     * @param array $data
     * @return string
     */
    public function DeliveryStationGet(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::DELIVERY_STATION_ORDER;
        return $this->addition($data);
    }


    /**
     * 服务站获得爱心值：33
     * @param array $data
     * @return string
     */
    public function ServiceStationGet(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::SERVICE_STATION_ORDER;
        return $this->addition($data);
    }

    /**
     * 抽奖获得爱心值：35
     * @param array $data
     * @return string
     */
    public function DrawGet(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::DRAW_AWARD;
        return $this->addition($data);
    }

    /**
     * 抽奖使用爱心值：36
     * @param array $data
     * @return string
     */
    public function DrawUsed(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::DRAW_AWARD_USED;
        return $this->subtraction($data);
    }

    /**
     * 抽奖奖励爱心值：37
     * @param array $data
     * @return string
     */
    public function DrawReward(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::DRAW_REWARD;
        return $this->addition($data);
    }

    /**
     * 助手可用转冻结 40
     * @param array $data
     * @return bool|string
     * @throws AppException
     */
    public function helperFroze(array $data)
    {
        $this->valueType = ConstService::VALUE_TYPE_USABLE;
        $this->source = ConstService::LOVE_HELPER_FROZE;
        $result = $this->subtraction($data);
        if ($result !== true) {
            \Log::info('助手可用变动失败');
        }else{
            $this->valueType = ConstService::VALUE_TYPE_FROZE;
            return $this->addition($this->data);
        }
    }

    /**
     * 实现基类中的抽项方法,获取会员model
     * @return mixed
     */
    public function getMemberModel()
    {
        $memberModel = Member::ofUid($this->data['member_id'])->with([
            'love' => function ($query) {
                $query->uniacid()->lockForUpdate();
            }
        ])->first();

        if ($memberModel && !$memberModel->love) $memberModel->setRelation('love', $this->defaultMemberLoveModel($memberModel->uid));

        return $memberModel;
    }

    private function defaultMemberLoveModel($memberId)
    {
        $memberLove = new MemberLove();

        $memberLove->uniacid = \YunShop::app()->uniacid;
        $memberLove->member_id = $memberId;
        $memberLove->usable = '0';
        $memberLove->froze = '0';

        return $memberLove;
    }


    /**
     * 实现基类中的抽项方法
     * @return bool
     * @throws AppException
     */
    public function validatorData()
    {
        if (bccomp($this->getNewValue(), 0, 2) == -1) {
            throw new AppException($this->getLoveName() . '剩余值不足');
        }
        if (!$this->relation()) {
            throw new AppException('该订单已经提交过，不能重复提交（LOVE）');
        }

        return true;
    }

    private function getValueType($valueType = '')
    {
        switch ($valueType) {
            case 'froze':
                return ConstService::VALUE_TYPE_FROZE;
                break;
            case 'usable':
                return ConstService::VALUE_TYPE_USABLE;
            default:
                return ConstService::VALUE_TYPE_FROZE;
        }
    }

    /**
     * 检测单号是否可用，为空则生成唯一单号
     * @return bool|string
     */
    private function relation()
    {
        if ($this->data['relation']) {
            if (!$this->relationValidate()) {
                return false;
            }
            return $this->data['relation'];
        }
        return $this->createOrderSN();
    }

    /**
     * @return bool
     */
    private function relationValidate()
    {
        !isset($this->relationSituation) && $this->relationSituation = $this->_relationValidate();

        return $this->relationSituation;
    }

    /**
     * @return bool
     */
    private function _relationValidate()
    {
        $result = LoveRecords::ofOrderSn($this->data['relation'])->ofSource($this->source)->ofMemberId($this->data['member_id'])->ofValueType($this->valueType)->ofOperator($this->data['operator'])->first();
        if ($result) {
            return false;
        }
        return true;
    }

    /**
     * 生成唯一单号
     * @return string
     */
    public function createOrderSN()
    {
        $order_sn = createNo('LC', true);
        while (1) {
            if (!LoveRecords::ofOrderSn($order_sn)->first()) {
                break;
            }
            $order_sn = createNo('LC', true);
        }
        return $order_sn;
    }

    /**
     * 实现基类中的抽项方法
     * @return bool
     * @throws AppException
     */
    public function recordSave()
    {
        $recordModel = new LoveRecords();

        $recordModel->fill($this->getRecordData());
        $validator = $recordModel->validator();
        if ($validator->fails()) {
            throw new AppException($this->getLoveName() . '验证出错');
        }
        if (!$recordModel->save()) {
            throw new AppException($this->getLoveName() . '记录写入出错');
        }
        return true;
    }

    /**
     * todo 可优化数据更新方式 20190717
     *
     * 实现基类中的抽项方法
     * @return bool
     * @throws AppException
     */
    public function updateMemberCredit()
    {
        $memberLove = $this->memberModel->love;

        //修改会员可用爱心值
        if ($this->valueType == ConstService::VALUE_TYPE_USABLE) {
            $memberLove->usable = $this->getNewValue();
        }
        //修改会员冻结爱心值
        if ($this->valueType == ConstService::VALUE_TYPE_FROZE) {
            $memberLove->froze = $this->getNewValue();
        }
        //如果是激活，更新激活时间
        if ($this->source == ConstService::SOURCE_ACTIVATION) {
            $memberLove->activation_at = time();
        }
        //dd($memberLove);
        if ($memberLove->save()) {
            $this->notice();
            return true;
        }
        throw new AppException('修改会员' . $this->getLoveName() . '错误');
    }

    /**
     * 获取爱心值变动 data 数组
     * @return array
     */
    private function getRecordData()
    {
        return [
            'uniacid'      => \YunShop::app()->uniacid,
            'member_id'    => $this->memberModel->uid,
            'old_value'    => $this->getMemberOldValue(),
            'change_value' => $this->change_value,
            'new_value'    => $this->getNewValue(),
            'type'         => $this->type,
            'source'       => $this->source,
            'relation'     => $this->relation(),
            'operator'     => $this->data['operator'],
            'operator_id'  => $this->data['operator_id'],
            'remark'       => $this->data['remark'],
            'value_type'   => $this->valueType
        ];
    }

    /**
     * 根据请求类型获取会员可用爱心值或冻结爱心值
     * @return string
     */
    protected function getMemberOldValue()
    {
        if ($this->valueType == ConstService::VALUE_TYPE_USABLE) {
            return isset($this->memberModel->love->usable) ? $this->memberModel->love->usable : "0";
        }
        if ($this->valueType == ConstService::VALUE_TYPE_FROZE) {
            return isset($this->memberModel->love->froze) ? $this->memberModel->love->froze : "0";
        }
        return "0";
    }

    /**
     * 计算改变后的值为多少
     * @return string
     */
    private function getNewValue()
    {
        return bcadd($this->getMemberOldValue(), $this->change_value, 2);
    }


    /**
     * 获取爱心值自定义名称
     * @return mixed|string
     */
    private function getLoveName()
    {
        return SetService::getLoveName();
    }


    /**
     * 爱心值变动通知
     */
    private function notice()
    {
        $temp_id = SetService::getLoveSet('change_temp_id');
        if (!$temp_id) {
            return;
        }
        $params = $this->getChangeNoticeContent();
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        \Log::debug('监听爱心值变动通知', $msg);
        if (!$msg) {
            return;
        }

        MessageService::notice(MessageTemp::$template_id, $msg, $this->memberModel->uid);
    }

    /**
     * 获取爱心值变动通知内容
     * @return mixed|string
     */
    private function getChangeNoticeContent()
    {
        $constService = new ConstService(SetService::getLoveName());

        $love_name = $constService->valueTypeComment()[$this->valueType];
        $source_name = $constService->sourceComment()[$this->source];

        $member_name = $this->memberModel->realname ?: $this->memberModel->nickname;
        $time = date('Y-m-d H:i:s', time());

        $params = [
            ['name' => '昵称', 'value' => $member_name],
            ['name' => '时间', 'value' => $time],
            ['name' => '变动值类型', 'value' => $love_name],
            ['name' => '变动数量', 'value' => $this->change_value],
            ['name' => '业务类型', 'value' => $source_name],
            ['name' => '当前剩余值', 'value' => $this->memberModel->love->usable],
            ['name' => '爱心值', 'value' => $love_name],
        ];

        //return "尊敬的".$member_name."，您于".$time."发生".$love_name."变动，变动值为".$this->change_value."，类型".$source_name."，您当前剩余".$love_name."为" . $this->getNewValue();
        return $params;
    }

}
