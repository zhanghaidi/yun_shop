<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 上午10:15
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;

use app\common\exceptions\AppException;
use app\common\models\MemberShopInfo;
use app\common\services\MessageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Common\Models\LoveActivationRecords;
use Yunshop\Love\Common\Models\LoveRecords;
use app\common\models\notice\MessageTemp;
use Yunshop\Love\Common\Models\Member;
use Yunshop\Love\Common\Models\MemberLove;
use Yunshop\Love\Common\Modules\LoveActivationRecord\LoveActivationRecordRepository;
use Yunshop\Love\Common\Modules\Member\YzMemberRepository;
use Yunshop\Love\Common\Modules\Repository;

class LoveActivationService
{
    private $memberModel;

    private $member_id;

    private $_model;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var ProfitActivationService
     */
    private $profitActivationService;

    /**
     * @var LoveActivationRecordRepository
     */
    private $loveActivationRecordRepository;

    /**
     * @var
     */
    private $memberRepository;

    /**
     * @var
     */
    private $yzMemberRepository;

    /**
     * @var Repository
     */
    private $memberLoveRepository;


    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->profitActivationService = new ProfitActivationService();

        $orderAmountRepository = $this->orderAmountRepository();
        $this->memberLoveRepository = $this->getMemberLoveRepository();

        $this->loveActivationRecordRepository = new LoveActivationRecordRepository(
            $this->getYzMemberRepository(),
            $orderAmountRepository,
            $this->getLoveRecordsRepository(),
            $this->memberLoveRepository
        );
    }

    /**
     * @return Repository
     */
    private function orderAmountRepository()
    {
        return new Repository($this->orderService->getActivationCompleteOrderMoneyData(), 'uid');
    }

    /**
     * @return Repository
     */
    private function getMemberLoveRepository()
    {
        $data = $this->getNeedActivationMemberLove();

        $result = new Repository($data, 'member_id');

        return $result;
    }

    private function getLoveRecordsRepository()
    {
        $data = LoveRecords::uniacid()->getQuery()->select(['member_id', 'change_value'])->whereIn('id', function ($query) {
            $query->select(DB::raw('max(id)'))
                ->from((new LoveRecords)->getTable())
                ->where('source', ConstService::SOURCE_TEAM_LEVEL_AWARD)
                ->where('value_type', ConstService::VALUE_TYPE_FROZE)
                ->where('type', ConstService::TYPE_INCOME)
                ->where('uniacid', \YunShop::app()->uniacid)
                ->groupBy('member_id');
        })->get();
        $result = new Repository($data, 'member_id');
        return $result;
    }

    /**
     * 会员关系仓库
     * @return YzMemberRepository|null
     */
    private function getYzMemberRepository()
    {
        if (!isset($this->yzMemberRepository)) {

            $members = MemberShopInfo::getQuery()
                ->select(['member_id', 'parent_id'])
                ->where('uniacid', \YunShop::app()->uniacid)
                ->get();

            $this->yzMemberRepository = new YzMemberRepository($members);
        }
        return $this->yzMemberRepository;
    }


    public function handleActivationQueue()
    {
        $result = $this->activationStart();
        if ($result !== true) {
            \Log::info('========爱心值激活UNIACID:' . \YunShop::app()->uniacid . '激活爱心值失败========');
        }
        \Log::info('========爱心值激活UNIACID:' . \YunShop::app()->uniacid . '激活爱心值完成========');
    }


    /**
     * 爱心值激活接口
     * @param $memberId
     * @return bool
     */
    public function loveActivation($memberId)
    {
        $this->member_id = $memberId;
        if (!$memberId) {
            return true;
        }

        if (!$this->getMemberModel()) {
            return true;
        }

        //激活值小于0不激活
        if (bccomp($this->getSumActivationLove(), 0, 2) != 1) {
            return true;
        }

        //会员冻结值为0不激活
        if (bccomp($this->getMemberFrozeLove(), 0, 2) != 1) {
            return true;
        }

        DB::transaction(function () {
            $result = $this->recordSave();
            if ($result !== true) {
                throw new AppException('激活记录写入失败');
            }
            $result = $this->updateMemberLove();
            if ($result !== true) {
                throw new AppException('更新会员' . $this->getLoveName() . '失败');
            }
            $this->notice();
            return true;
        });
        return true;
    }

    /**
     * 爱心值激活记录保存
     * @return bool
     * @throws AppException
     */
    private function recordSave()
    {
        $this->_model = new LoveActivationRecords();

        $this->_model->fill($this->getRecordData());
        $validator = $this->_model->validator();
        if ($validator->fails()) {
            throw new AppException('激活记录数据有误');
        }
        return $this->_model->save();
    }

    /**
     * 激活会员冻结爱心值
     *
     * @return string
     * @throws AppException
     */
    private function updateMemberLove()
    {
        return (new LoveChangeService())->activation($this->getChangeRecordData());
    }

    /**
     * 获取激活记录 data 数组
     * @return array
     */
    private function getRecordData()
    {
        return [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $this->member_id,
            'member_froze_love' => $this->getMemberFrozeLove(),
            'fixed_proportion' => $this->getFixedActivationProportion(),
            'fixed_activation_love' => $this->getFixedActivationLove(),
            'first_order_money' => $this->getLv1CompleteOrderMoney(),
            'first_proportion' => $this->getLv1Proportion(),
            'first_activation_love' => $this->getLv1ActivationLove(),
            'second_three_order_money' => $this->getLv2AndLv3CompleteOrderMoney(),
            'second_three_proportion' => $this->getLv2AndLv3ProPortion(),
            'last_upgrade_team_leve_award' => $this->getLastUpgradeTeamLevelAward(),
            'second_three_fetter_proportion' => $this->getLevelTwoFetterProportion(),
            'second_three_activation_love' => $this->getLv2AndLv3ActivationLove(),
            //周期订单利润比例激活
            'froze_total' => $this->profitActivationService->getFrozeTotal(),
            'profit_proportion' => $this->profitActivationService->getProfitProportion(),
            'cycle_order_profit' => $this->profitActivationService->getCycleOrderProfit(),
            'profit_activation_love' => $this->getProfitActivationLove(),
            //增加团队激活
            'team_order_money' => $this->getTeamCompleteOrderMoney(),
            'team_proportion' => $this->getTeamProportion(),
            'team_activation_love' => $this->getTeamActivationLove(),
            'sum_activation_love' => $this->getSumActivationLove(),
            'order_sn' => $this->getOrderSN(),
            'actual_activation_love' => $this->getActualActivationLove(),
            'surplus_froze_love' => $this->getSurplusFrozeLove(),
            'day_time' => date('Y-m-d')
        ];
    }

    private function getSurplusFrozeLove()
    {
        return bcsub($this->getMemberFrozeLove(), $this->getActualActivationLove(), 2);
    }

    /**
     * 获取更新会员爱心值 data 数组
     * @return array
     */
    private function getChangeRecordData()
    {
        return [
            'member_id' => $this->member_id,
            'change_value' => $this->_model->actual_activation_love,
            'operator' => ConstService::OPERATOR_SHOP,
            'operator_id' => '0',
            'remark' => $this->getChangeRecordRemark(),
            'relation' => $this->_model->order_sn
        ];
    }

    /**
     * 获取更新会员爱心值备注
     * @return string
     */
    private function getChangeRecordRemark()
    {
        $love = $this->getLoveName();
        return $love . "激活，本次应激活" . $this->_model->sum_activation_love . "元，实际激活值为" . $this->getActualActivationLove();
    }

    /**
     * 获取实际激活爱心值
     * @return string
     */
    private function getActualActivationLove()
    {
        $sum = $this->getSumActivationLove();
        $froze = $this->getMemberFrozeLove();
        return bccomp($sum, $froze, 2) == 1 ? $froze : $sum;
    }

    /**
     * 获取一级下线完成订单总金额
     * @return mixed
     */
    private function getLv1CompleteOrderMoney()
    {
        $loveActivationRecord = $this->loveActivationRecordRepository->find($this->member_id);
        if (!$loveActivationRecord) {
            return 0;
        }
        return $loveActivationRecord->getChildrenTeamAmountGroup(1);
    }

    /**
     * 获取二级下线完成订单总金额
     * @return mixed
     */
    private function getLv2CompleteOrderMoney()
    {
        $loveActivationRecord = $this->loveActivationRecordRepository->find($this->member_id);
        if (!$loveActivationRecord) {
            return 0;
        }
        return $loveActivationRecord->getChildrenTeamAmountGroup(2);
    }

    /**
     * 获取三级下线完成订单总金额
     * @return mixed
     */
    private function getLv3CompleteOrderMoney()
    {
        $loveActivationRecord = $this->loveActivationRecordRepository->find($this->member_id);
        if (!$loveActivationRecord) {
            return 0;
        }
        return $loveActivationRecord->getChildrenTeamAmountGroup(3);
    }

    /**
     * 获取一级下线完成订单激活比例
     * @return string
     */
    private function getLv1Proportion()
    {
        return SetService::getLv1ProPortion();
    }

    /**
     * 获取二、三级下线完成订单激活比例
     * @return string
     */
    private function getLv2AndLv3Proportion()
    {
        return SetService::getLv2AndLv3ProPortion();
    }

    private function getTeamProportion()
    {
        return SetService::getTeamProportion();
    }

    /**
     * 获取会员二、三级下线完成订单总金额
     * @return string
     */
    private function getLv2AndLv3CompleteOrderMoney()
    {
        return bcadd($this->getLv2CompleteOrderMoney(), $this->getLv3CompleteOrderMoney(), 2);
    }

    private function getTeamCompleteOrderMoney()
    {
        $loveActivationRecord = $this->loveActivationRecordRepository->find($this->member_id);
        if (!$loveActivationRecord) {
            return 0;
        }
        return $loveActivationRecord->getChildrenTeamAmount();
    }


    /**
     * 固定激活  N% 的爱心值
     * @return string
     */
    private function getFixedActivationLove()
    {
        $proportion = $this->getFixedActivationProportion();

        return empty($proportion) ? '0' : $this->proportionMath($this->getMemberFrozeLove(), $proportion);
    }

    /**
     * 激活一级下线每周订单金额 N% 的爱心值
     * @return string
     */
    private function getLv1ActivationLove()
    {
        $proportion = $this->getLv1Proportion();

        return empty($proportion) ? 0 : $this->proportionMath($this->getLv1CompleteOrderMoney(), $proportion);
    }


    /**
     * 激活二、三下线每周订单金额的 N% 的爱心值
     * @return string
     */
    private function getLv2AndLv3ActivationLove()
    {
        $proportion = $this->getLv2AndLv3Proportion();

        if (!empty($proportion)) {
            $sum = $this->proportionMath($this->getLv2AndLv3CompleteOrderMoney(), $proportion);
            //如果上限比例为空或者填写为零：则无上限金额限制
            //如果设置最高上限比例，但是代理没有升级赠送爱心值，则为0，无二、三级会员下线订单金额激活
            $fetterProPortion = $this->getLevelTwoFetterProportion();
            if ($fetterProPortion > 0) {
                $fetter = $this->getLv2AndLv3ActivationFetter();
                return bccomp($sum, $fetter, 2) == -1 ? $sum : $fetter;
            }
            return $sum;
        }
        return '0';
    }


    private function getTeamActivationLove()
    {
        $proportion = $this->getTeamProportion();

        if (!empty($proportion)) {
            $sum = $this->proportionMath($this->getTeamCompleteOrderMoney(), $proportion);
            $fetter = $this->getLv2AndLv3ActivationFetter();

            return bccomp($sum, $fetter, 2) == -1 ? $sum : $fetter;
        }
        return '0';
    }

    private function getProfitActivationLove()
    {
        $proportion = $this->profitActivationService->getProfitProportion();

        if ($proportion && $proportion > 0) {

            $hold = $this->getMemberFrozeLove();

            $frozeTotal = $this->profitActivationService->getFrozeTotal();
            $cycleOrderProfit = $this->profitActivationService->getCycleOrderProfit();

            //（客户持有冻结量÷总持有冻结量）×（平台上周期完成订单利润（完成时间）×设定比例）
            $amount = $hold / $frozeTotal * $cycleOrderProfit * $proportion / 100;

            return bcadd($amount, 0, 2);
        }

        return '0.00';
    }

    private function getLevelTwoFetterProportion()
    {
        return SetService::getLevelTwoFetterProportion();
    }

    private function getFixedActivationProportion()
    {
        return SetService::getFixedActivationProportion();
    }

    /**
     * 获取代理商最后一次升级赠送的爱心值
     * @param $memberId
     * @return string
     */
    private function getLastUpgradeTeamLevelAward()
    {
        $loveActivationRecord = $this->loveActivationRecordRepository->find($this->member_id);
        if (!$loveActivationRecord) {
            return 0;
        }
        return $loveActivationRecord->getLastUpgradeTeamLevelAward()['change_value'] ?: 0;
    }

    /**
     * 获取二、三级下线最高奖励爱心值
     * @return string
     */
    private function getLv2AndLv3ActivationFetter()
    {
        return $this->proportionMath($this->getLastUpgradeTeamLevelAward(), $this->getLevelTwoFetterProportion());
    }

    /**
     * 获取本次激活爱心值总和
     * @return string
     */
    private function getSumActivationLove()
    {
        //
        $sumActivation = bcadd($this->getLv1ActivationLove(), $this->getLv2AndLv3ActivationLove(), 2);

        // 增加团队激活值
        $sumActivation = bcadd($sumActivation, $this->getTeamActivationLove(), 2);

        // 增加利润激活值
        $sumActivation = bcadd($sumActivation, $this->getProfitActivationLove(), 2);

        return bcadd($sumActivation, $this->getFixedActivationLove(), 2);
    }

    /**
     * 比例运算数学
     * @param $money
     * @param $proportion
     * @return string
     */
    private function proportionMath($money, $proportion)
    {
        return bcdiv(bcmul($money, $proportion, 2), 100, 2);
    }

    /**
     * 生成订单号
     * @return string
     */
    private function getOrderSN()
    {
        $ordersn = createNo('AL', true);
        while (1) {
            if (!LoveActivationRecords::ofOrderSn($ordersn)->first()) {
                break;
            }
            $ordersn = createNo('AL', true);
        }
        return $ordersn;
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
     * 获取会员冻结爱心值
     * @return string
     */
    private function getMemberFrozeLove()
    {
        $loveActivationRecord = $this->loveActivationRecordRepository->find($this->member_id);

        if (!$loveActivationRecord) {
            return 0;
        }
        return $loveActivationRecord->getMemberLove()['froze'] ?: 0;
    }

    private function getMemberRepository(){
        if(!isset($this->memberRepository)){
            $data = Member::getQuery()->select(['uid','nickname','realname'])->where('uniacid',\YunShop::app()->uniacid)->get();
            $this->memberRepository = new Repository($data,'uid');
        }
        return $this->memberRepository;
    }
    /**
     * 获取会员爱心值信息
     * @return mixed
     */
    private function getMemberModel()
    {

        return $this->memberModel = $this->getMemberRepository()->find($this->member_id);
    }


    /**
     * 获取公众号激活时间设置
     * @return string
     */
    private function getActivationTime()
    {
        return SetService::getActivationTime();
    }

    /**
     * 爱心值激活
     * @return bool
     */
    private function activationStart()
    {
        $memberIds = $this->memberLoveRepository->all() ?: [];
        foreach ($memberIds as $key=>$member) {
            $result = $this->loveActivation($member['member_id']);
            if ($result !== true) {
                \Log::info('Uniacid:' . \YunShop::app()->uniacid . '会员' . $member['member_id'] . '激活爱心值失败');
                continue;
            }
        }
        return true;
    }


    private function getNeedActivationMemberLove()
    {
        switch (SetService::getActivationTime()) {
            case 2:
                $startTime = Carbon::now()->subWeek(1)->startOfWeek()->timestamp;
                break;
            case 3:
                $startTime = (new Carbon('first day of last month'))->startOfDay()->timestamp;
                break;
            default:
                $startTime = Carbon::now()->startOfDay()->timestamp;
        }

        $memberLove = MemberLove::uniacid()->getQuery()->select('member_id', 'froze', 'activation_at');

        $memberLove = $memberLove->where('activation_at', '<', $startTime);

        $memberIds = $memberLove->where('froze', '>', '0')->get();

        return $memberIds;
    }

    private $msg;

    private function getMsg()
    {
        $temp_id = SetService::getLoveSet('activation_temp_id');
        if (!$temp_id) {
            return '';
        }
        $params = $this->getActivationNoticeContent();

        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return '';
        }

        return $msg ?: '';
    }

    private function notice()
    {
        if (!isset($this->msg)) {
            $this->msg = $this->getMsg();
        }
        MessageService::notice(MessageTemp::$template_id, $this->msg, $this->memberModel->member_id);
    }

    private function getActivationNoticeContent()
    {
        $member_name = $this->memberModel->realname ?: $this->memberModel->nickname;
        $time = date('Y-m-d H:i:s', time());

        $params = [
            ['name' => '昵称', 'value' => $member_name],
            ['name' => '时间', 'value' => $time],
            ['name' => '激活值', 'value' => $this->_model->actual_activation_love],
            ['name' => '固定比例激活值', 'value' => $this->_model->fixed_activation_love],
            ['name' => '上周一级下线激活值', 'value' => $this->_model->first_activation_love],
            ['name' => '上周二级三级会员下线激活值', 'value' => $this->_model->second_three_activation_love],
        ];
        return $params;
    }
}
