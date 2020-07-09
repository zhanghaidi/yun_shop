<?php
namespace Yunshop\ClockIn\admin;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use Yunshop\ClockIn\services\ClockInService;
use Yunshop\ClockIn\models\ClockRuleModel;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\TeamDividend\models\TeamDividendLevelModel;


/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/07
 * Time:14:28
 */

class ClockInSetController extends BaseController
{

    public function index()
    {
        $exist_commission = app('plugins')->isEnabled('commission');
        $exist_team = app('plugins')->isEnabled('team-dividend');
        if ($exist_commission) {
            $agent_levels = AgentLevel::getLevels()->get();
            if ($agent_levels->isEmpty()) {
                $agent_levels = [];
            }
        }
        if ($exist_team) {
            $team_levels = TeamDividendLevelModel::getList()->get();
            if ($team_levels->isEmpty()) {
                $team_levels = [];
            }
        }

        $clockInService = new ClockInService();

        $clockRuleModel = new ClockRuleModel();

        $set = Setting::get('plugin.clock_in');


        $clockRule = $clockRuleModel->getRule();

        if (!isset($set['content']) || !empty($clockRule)) {

            $set['content'] = $clockRule->rule_content;
        }

        $requestModel = \YunShop::request()->setdata;
        if ($requestModel) {

            if ($exist_commission) {
                $requestModel['first_level'] = intval(trim($requestModel['first_level'])) < 0 ? 0 : intval(trim($requestModel['first_level']));
                $requestModel['second_level'] = intval(trim($requestModel['second_level'])) < 0 ? 0 : intval(trim($requestModel['second_level']));
                $requestModel['third_level'] = intval(trim($requestModel['third_level'])) < 0 ? 0 : intval(trim($requestModel['third_level']));
            }

            //从基础设置中分离content字段
            if (!$clockRule) {
                $clockRule = $clockRuleModel;
                $clockRule['uniacid'] =  \Yunshop::app()->uniacid;
            }
            $clockRule['rule_content'] = $requestModel['content'];

            if ($clockRule->save()) {
                unset($requestModel['content']); 
            }
            if (Setting::set('plugin.clock_in', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.clock-in.admin.clock-in-set.index'));
            } else {
                return $this->error('设置失败');
            }
        }
        $pluginName = $clockInService->get('plugin_name');

        for ($i = 0; $i <= 23; $i++) {
            $hourData[$i] = [
                'key' => $i,
                'name' => $i . ":00 点",
            ];
        }

        $payMethod = $clockInService->get('pay_method');
        return view('Yunshop\ClockIn::admin.set', [
            'set' => $set,
            'pluginName' => $pluginName,
            'hourData' => $hourData,
            'payMethod' => $payMethod,
            'isOpenCommission' => $exist_commission,
            'agent_levels' => $agent_levels,
            'isOpenTeam' => $exist_team,
            'team_levels' => $team_levels
        ])->render();
    }


}