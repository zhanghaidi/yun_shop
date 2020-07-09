<?php
namespace Yunshop\Commission\services;

use app\common\models\McMappingFans;
use app\common\models\Member;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\YzMember;


/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/15
 * Time: 下午3:05
 */
class AgentService
{
    public static function getAgentData($agents)
    {
        if ($agents) {
            foreach ($agents as &$agent) {
                $agent['lowers'] = Agents::getLower($agent->member_id, '', true)->count();
            }
            unset($agent);
        }
        return $agents;
    }

    /**
     * @param array $search
     * @return mixed
     */
    public static function getSearch($search)
    {
        $search['is_time'] = $search['is_time'] ? $search['is_time'] : 0;

        if (!isset($search['starttime']) || $search['starttime'] != '请选择') {
            $search['starttime'] = strtotime('-1 month');
        }

        if (!isset($search['endtime']) || $search['endtime'] != '请选择') {
            $search['endtime'] = time();
        }

        $search['member'] = isset($search['member']) ? $search['member'] : '';
        $search['follow'] = isset($search['follow']) ? $search['follow'] : '';
        $search['parent_id'] = isset($search['parent_id']) ? $search['parent_id'] : '';
        $search['parent_name'] = isset($search['parent_name']) ? $search['parent_name'] : '';
        $search['level'] = isset($search['level']) ? $search['level'] : '';
        $search['lower'] = isset($search['lower']) ? $search['lower'] : '';
        $search['isagent'] = isset($search['isagent']) ? $search['isagent'] : '';
        $search['black'] = isset($search['black']) ? $search['black'] : '';

        return $search;
    }

    /**
     * @param $requestAgents
     * @param $set
     * @return mixed
     * 确认分销商层级
     */
    public static function getParentAgents($requestAgents, $set)
    {
        $agentData = [];
        // 如果开启内购并且该会员是分销商，该会员为一级
        $first_level = $set['self_buy'] && $requestAgents['is_agent'] ? $requestAgents : $requestAgents['belongs_to_parent'];
        $second_level = $first_level['belongs_to_parent'];
        $third_level = $second_level['belongs_to_parent'];
        unset($first_level['belongs_to_parent']);
        unset($second_level['belongs_to_parent']);
        
        if ($set['level'] >= 1) {
            $agentData['first_level'] = $first_level;
        }
        if ($set['level'] >= 2) {
            $agentData['second_level'] = $second_level;
        }

        if ($set['level'] == 3) {
            $agentData['third_level'] = $third_level;
        }
        return $agentData;
    }

    public function getMemberIdByLevelId($levelId)
    {
        $member = Agents::getMemberIdByLevelId($levelId)
        ->get();

        return $member;
    }

    public static function getFirstAgentByUid($uid)
    {
        $member = YzMember::getMemberByMemberId($uid)->first();
        $agent = Agents::getAgentByMemberId($uid)->first();
        // 自己不是分销商, 上级分销商为一级
        if (!$agent) {
            file_put_contents(storage_path('logs/Y0914.txt'),  print_r('time:'.date('Y-m-d H:i:s').',UID['.$uid.']要进行升级'.PHP_EOL,1), FILE_APPEND);
            $agent = Agents::getAgentByMemberId($member->parent_id)->first();
        }
        return $agent;
    }
}