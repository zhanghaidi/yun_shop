<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/17
 * Time: 上午9:49
 */

namespace Yunshop\ClockIn\services;


use app\common\models\Income;
use app\common\models\Member;
use Yunshop\TeamDividend\models\TeamDividendAgencyModel;
use Yunshop\TeamDividend\models\TeamDividendModel;
use Yunshop\TeamDividend\models\YzMemberModel;
use Yunshop\TeamDividend\services\MessageService;
use Yunshop\TeamDividend\services\OrderCreatedService;
use Yunshop\TeamDividend\services\UpgradeService;

class TeamService
{
    private $buy_uid;
    private $amount;
    private $set;
    private $AgentData;
    private $Rate;
    private $AwardHierarchy;

    public function __construct($uid, $amount)
    {
        $this->amount = $amount;
        $this->buy_uid = $uid;
        $this->set = \Setting::get('plugin.clock_in');
    }

    public function handle()
    {
        // 没有 或 未开启插件，返回
        $exist_team = app('plugins')->isEnabled('team-dividend');
        if (!$exist_team) {
            return;
        }

        // 如果 打卡设置未开启，返回
        if (!$this->set['is_team_dividend']) {
            return;
        }

        $buyMember = Member::getUserInfos($this->buy_uid)->first();

        if (app('plugins')->isEnabled('commission')) {
            $clock_set = \Setting::get('plugin.clock_in');
            $clock_name = $clock_set['plugin_name'] ?: '早起打卡';
        }

        $dividendData = [
            'uniacid' => \YunShop::app()->uniacid,
            'order_sn' => $clock_name . '经销商奖励',
            'order_amount' => $this->amount,
            'amount' => $this->amount,
            'settle_days' => 0, //没有结算期
            'type' => 0,
            'status' => 1,
            'create_month' => date('Y-m'),
            'created_at' => time(),
            'recrive_at' => time()
        ];

        $agents = $this->superior($this->buy_uid);
        if(!$agents){
            return;
        }
        foreach ($agents as $key => $agent) {
            //等级分红层数
            $this->AwardHierarchy[$agent['level']]['level'] = $agent['level'];
            if (!isset($this->AwardHierarchy[$agent['level']]['hierarchy'])) {
                $this->AwardHierarchy[$agent['level']]['hierarchy'] = 0;
            }
            //团队分红
            $this->dividend($dividendData,$agent,$buyMember);
            //平级奖 处理
            //$this->hierarchy($dividendData,$agent,$buyMember);
        }
    }

    private function dividend($dividendData,$agent,$buyMember)
    {
        $ratio = $this->set['team_level'][$agent['has_one_level']['id']]['dividend_ratio'];
        if ($ratio < 0) {
            $ratio = 0;
        }
        $dividendRate = $ratio - $agent['rate']; // 强制极差分红
        //分红数据
        $dividendData['member_id'] = $agent['uid'];
        $dividendData['agent_level'] = $agent['level'];
        $dividendData['dividend_rate'] = $dividendRate;
        $dividendData['lower_level_rate'] = $agent['rate'];
        $dividendData['dividend_amount'] = round($dividendData['amount'] / 100 * $dividendRate, 2);
        //比例大于0 是分红
        if ($dividendRate > 0 && $this->AwardHierarchy[$agent['level']]['hierarchy'] == 0) {
            $this->addDividend($dividendData, $agent);
            $member = Member::getMemberByUid($agent['uid'])->with('hasOneFans')->first();
            $notice = [
                'lower_level_name' => $buyMember->nickname,
                'order_amount' => $dividendData['order_amount'],
                'amount' => $dividendData['amount'],
                'dividendRate' => $dividendRate,
                'rate' => $agent['rate'],
                'dividend_amount' => $dividendData['dividend_amount'],
            ];
            MessageService::dividendOrder($notice, $member->hasOneFans);
        }
    }

    private function addDividend($dividendData, $agent)
    {
        $model = TeamDividendModel::create($dividendData);
        if ($model) {
            OrderCreatedService::setAareaDividend($dividendData, $agent);
        }
        OrderCreatedService::setAareaDividend($dividendData, $agent);
        //增加团队代理已结算金额
        $this->addAgentDividend($model);
        //结算分红升级
        (new UpgradeService())->upgradeForSettle($model->member_id);
        //加入分红收入
        $this->addAreaDividendIncome($model);
    }

    private function addAgentDividend($model)
    {
        $data = [
            'dividend_final' => $model->hasOneAgent->dividend_final + $model->dividend_amount,
            'dividend_open' => $model->hasOneAgent->dividend_open - $model->dividend_amount,
        ];
        TeamDividendAgencyModel::updatedAgentById($data,$model->hasOneAgent->id);
    }

    private function addAreaDividendIncome($model)
    {
        if (app('plugins')->isEnabled('commission')) {
            $clock_set = \Setting::get('plugin.clock_in');
            $clock_name = $clock_set['plugin_name'] ?: '早起打卡';
        }

        //收入数据
        $incomeData = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $model->member_id,
            'incometable_type' => TeamDividendModel::class,
            'incometable_id' => $model->id,
            'type_name' => $clock_name . '经销商奖励',
            'amount' => $model->dividend_amount,
            'status' => '0',
            'detail' => '',
            'create_month' => date("Y-m"),
        ];
        //插入收入
        $incomeModel = new Income();
        $incomeModel->setRawAttributes($incomeData);
        $incomeModel->save();
        \Log::info(time() . ":收入统计插入数据!");
    }

    private function hierarchy($dividendData,$agent,$buyMember)
    {
        if ($this->AwardHierarchy[$agent['level']]['level'] == $agent['level']) {

            $award_hierarchy = $this->set['team_level'][$agent['has_one_level']['id']]['award_hierarchy'];
            if ($this->AwardHierarchy[$agent['level']]['hierarchy'] > 0
                && $this->AwardHierarchy[$agent['level']]['hierarchy'] <= $award_hierarchy
            ) {
                $dividendData['member_id'] = $agent['uid'];
                $dividendData['agent_level'] = $agent['level'];
                $dividendData['dividend_rate'] = $this->set['team_level'][$agent['has_one_level']['id']]['award_ratio'];
                //$dividendData['dividend_rate'] = $agent['has_one_level']['award_ratio'];
                $dividendData['lower_level_rate'] = 0;
                $dividendData['type'] = 1;
                $dividendData['hierarchy'] = $this->AwardHierarchy[$agent['level']]['hierarchy'];
                $dividendData['dividend_amount'] = round($dividendData['amount'] / 100 * $this->set['team_level'][$agent['has_one_level']['id']]['award_ratio'], 2);
                //$dividendData['dividend_amount'] = round($dividendData['amount'] / 100 * $agent['has_one_level']['award_ratio'], 2);
                if ($dividendData['dividend_amount'] > 0) {
                    $this->addDividend($dividendData, $agent);
                    /*$member = Member::getMemberByUid($agent['uid'])->with('hasOneFans')->first();
                    $notice = [
                        'lower_level_name' => $buyMember->nickname,
                        'order_amount' => $dividendData['order_amount'],
                        'amount' => $dividendData['amount'],
                        'dividendRate' => $agent['has_one_level']['award_ratio'],
                        'dividend_amount' => $dividendData['dividend_amount'],
                    ];
                    MessageService::flatPrize($notice, $member->hasOneFans);*/
                }
            }
            $this->AwardHierarchy[$agent['level']]['hierarchy']++;// 平级奖层数增加
        }
    }

    private function superior($memberId)
    {
        if (!$memberId) {
            return $this->AgentData;
        }
        $request = YzMemberModel::getSuperiorById($memberId)->first();
        if ($request->hasOneMember) {
            $data = $request->hasOneMember;
            $agency = TeamDividendAgencyModel::getAgencyInfoByUid($data->member_id);
            if ($agency) {
                $this->AgentData[$data->member_id] = $agency->toArray();
                $this->AgentData[$data->member_id]['rate'] = $this->Rate ? $this->Rate : 0;

                $this->Rate = $this->set['team_level'][$agency->hasOneLevel->id]['dividend_ratio'] ?: 0;
                //dd($this->set['team_level'][$agency->hasOneLevel->id]);
                //$this->Rate = $agency->hasOneLevel->dividend_ratio;
            }
            self::superior($data->member_id);
        }
        return $this->AgentData;
    }
}