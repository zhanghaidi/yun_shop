<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/24
 * Time: 下午8:14
 */

namespace Yunshop\Commission\services;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\Member;
use app\common\models\UniAccount;
use app\common\services\finance\PointService;
use EasyWeChat\Support\Log;
use Illuminate\Support\Facades\DB;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\CommissionManage;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\Income;
use Yunshop\Commission\models\Lose;
use Yunshop\Commission\models\YzMember;
use Yunshop\Commission\services\UpgradeService;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Common\Services\LoveChangeService;

class TimedTaskService
{
    /**
     * 佣金结算处理
     * @throws \app\common\exceptions\ShopException
     */
    public function handle()
    {
        \Log::info("--分销定时任务--");
        $config = \app\backend\modules\income\Income::current()->getItem('commission');

        $uniAccount = UniAccount::getEnable();

        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $set = Setting::get('plugin.commission');
            $pointSet = Setting::get('point.set');

            //结算修复
            (new FixService())->handle($set);
            //结算类型为手动结算 跳过
            if (isset($set['settlement_model']) && $set['settlement_model'] == 1) {
                \Log::info("--分销--uniacid[{$u->uniacid}]手动结算");
                continue;
            }
            $requestOrder = CommissionOrder::getStatement()->toArray();
            if ($requestOrder) {
                \Log::info("--分销--uniacid[{$u->uniacid}]结算ing");
                $times = time();
                $request = $this->updatedStatement($times);
                foreach ($requestOrder as $item) {

                    $amount = $item['commission'];
                    if (app('plugins')->isEnabled('amount-seal')) {
                        $set = Setting::get('plugin.amount_seal');
                        if ($set['is_open'] == 1) {
                            $sealData = \Yunshop\AmountSeal\model\Member::getAmount($item['member_id'], $amount);
                            $amount = $sealData['reality_amount'];
                            $sealData['award_id'] = $item['id'];
                            Lose::create($sealData);
                        }
                    }
                    if ($amount > 0) {
                        $item['commission'] = $amount;
                    } else {
                        // 无效
                        CommissionOrder::where('id', $item['id'])->update(['status' => -1]);
                        continue;
                    }

                    //更新累计佣金
                    $requestAgent = $this->updateCommission($item);
                    //判断结算选项
                    if (isset($set['settlement_option']) && $set['settlement_option'] == 1) {
                        //转入爱心值
                        $this->addPoint($item, $pointSet);
                        $this->updatedWithdraw($item);
                    } else {
                        //插入收入
                        $this->addIncome($item, $times, $config);
                    }
                }
            }
        }

    }

    public function updatedWithdraw($item)
    {
        $commissionOrder = CommissionOrder::find($item['id']);
        $commissionOrder->update([
            'withdraw' => '2',
        ]);
    }

    /**
     * @param $times
     * @return mixed
     */
    public function updatedStatement($times)
    {
        $request = CommissionOrder::updatedStatement($times);
        if ($request) {
            \Log::info($times . ":结算" . $request . "条佣金订单.");
        }
        return $request;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function updateCommission($item)
    {
        $requestAgent = Agents::updateCommission($item['commission'], $item['member_id'], 'plus');

//        $this->setManage($item);
        if ($requestAgent) {
            // 佣金升级
            UpgradeService::commission($item['member_id']);
        }
        return $requestAgent;
    }

//    public function setManage($item)
//    {
//        \Log::info('分销管理奖开始');
//        $commissionSet = Setting::get('plugin.commission');
//        $set = Setting::get('plugin.commission_manage');
//        if(!$set['is_manage']){
//            \Log::info('分销管理奖关闭');
//            return;
//        }
//        $set['level'] = $commissionSet['level'];
//        //获取上级关系链
//        $request_agents = YzMember::getParentAgents($item['member_id'], false)->first();
//        if(!$request_agents){
//            \Log::info('分销管理奖,未获得上级关系链');
//            return;
//        }
//        $request_agents =  $request_agents->toArray();
//        //确认分销商层级
//        $agents = AgentService::getParentAgents($request_agents, $set);
//        foreach ($agents as $level => $agent) {
//            if (empty($agent['agent']) || $agent['agent']['is_black']) {
//                continue;
//            }
//            //默认等级 是否开启管理奖赋值
//            if(!$agent['agent']['agent_level']){
//                $agent['agent']['agent_level']['is_manage'] = $set['is_default_level'];
//            }
//            // 验证等级是否开启管理奖
//            if (!$agent['agent']['agent_level']['is_manage']){
//                \Log::info('层级'.$level.'级 会员ID:'.$agent['member_id'].' 分销等级未开启管理奖');
//                continue;
//            }
//
//            //该分销商所在层级
//            $hierarchy = CommissionOrderService::getHierarchy($level);
//            $agent['agent']['hierarchy'] = $level;
//            //分销订单数据
//            $orderData = [
//                'uniacid' => \YunShop::app()->uniacid,
//                'member_id' => $agent['member_id'],
//                'subordinate_id' => $item['member_id'],
//                'subordinate_commission' => $item['commission'],
//                'hierarchy' => $hierarchy,
//                'manage_rage' => $set[$level],
//                'manage_amount' => $item['commission'] / 100 * $set[$level],
//                'created_at' => time(),
//            ];
//            \Log::info('管理奖数据',$orderData);
//            $id = CommissionManage::insertGetId($orderData);
//            $config = \app\backend\modules\income\Income::current()->getItem('manage');
//            //收入数据
//            $incomeData = [
//                'uniacid' => \YunShop::app()->uniacid,
//                'member_id' => $agent['member_id'],
//                'incometable_type' => $config['class'],
//                'incometable_id' => $id,
//                'type_name' => $config['title'],
//                'amount' => $orderData['manage_amount'],
//                'status' => '0',
//                'detail' => '',
//                'create_month' => date("Y-m"),
//            ];
//            \Log::info('管理奖收入数据',$incomeData);
//            //插入收入
//            $incomeModel = new Income();
//            $incomeModel->setRawAttributes($incomeData);
//            if ($incomeModel->save()) {
//                \Log::info(":管理奖 收入统计插入数据!");
//            }
//        }
//        \Log::info(":管理奖结束");
//    }


    /**
     * @param $commissionData
     * @param $times
     * @param $config
     */
    public function addIncome($commissionData, $times, $config)
    {
        $data = CommissionOrderService::getIncomeDetail($commissionData, $times);
        //收入数据
        $incomeData = CommissionOrderService::getIncomeData($commissionData, $config, $data);

        \Log::info('收入插入数据:', $incomeData);
        $incomeModel = new Income();
        $incomeModel->setRawAttributes($incomeData);
        if ($incomeModel->save()) {

            $this->noticeData($commissionData);

            \Log::info($times . ":收入统计插入数据!");
        }
    }

    /**
     * @param $commissionData
     * @param $pointSet
     * @throws \app\common\exceptions\ShopException
     */
    public function addPoint($commissionData, $pointSet)
    {
        $chang_point = $this->calculationPoint($commissionData['commission'], $pointSet);
        $point_data = [
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode'        => PointService::POINT_MODE_COMMISSION_TRANSFER,
            'member_id'         => $commissionData['member_id'],
            'point'             => $chang_point,
            'remark'            => '分销商分红转入'
        ];

        (new PointService($point_data))->changePoint();
    }

    public function calculationPoint($commission_amount, $pointSet)
    {
        return round($commission_amount * 1/$pointSet['money'], 2);
    }

    /**
     * @param $commissionData
     */
    public function noticeData($commissionData)
    {
        $member = Member::getMemberByUid($commissionData['member_id'])->with('hasOneFans')->first();
        $notice = [
            'amount' => $commissionData['commission'],
            'agent' => $member->hasOneFans,
        ];
        MessageService::statement($notice);
    }
}