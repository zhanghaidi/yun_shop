<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/21
 * Time: 下午3:35
 */

namespace Yunshop\Micro\Listener;

use app\common\events\member\MemberGoldEvent;
use app\common\models\notice\MessageTemp;
use app\common\services\member\HandleService;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Micro\common\services\MicroShop\MicroShopService;
use app\backend\modules\member\models\Member;

class AgentMicroBonusListener
{
    public $event;
    public $result;
    public $agent_micro;
    public $set;
    public $num = 0;
    public $ratio;
    public $uid;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(MemberGoldEvent::class, self::class . '@onHandle');
    }

    public function onHandle(MemberGoldEvent $event)
    {
        $this->event = $event;
        $this->result = $event->getMemberIdAndGold();
        $this->verify();
    }

    public function verify()
    {
        $this->set = \Setting::get('plugin.micro');
        if ($this->set['agent_gold_level'] != 0) {
            $this->handleAgentGold($this->result['member_id']);
        }
        return;
    }

    public function handleAgentGold($uid)
    {
        if ($this->num == $this->set['agent_gold_level']) {
            return;
        }
        $micro = MicroShopService::verifyAgentMicroShop($uid);
        if (!$micro) {
            return;
        }
        $this->uid = $micro->member_id;
        $this->trigger();
        $this->num += 1;
        $this->handleAgentGold($micro->member_id);
    }

    public function getAgentGoldRatio()
    {
        if (($this->num + 1) == 1) {
            $this->ratio = $this->set['gold_first_level'];
        } else if (($this->num + 1) == 2) {
            $this->ratio = $this->set['gold_second_level'];
        } else if (($this->num + 1) == 3) {
            $this->ratio = $this->set['gold_third_level'];
        } else {
            $this->ratio = 0;
        }
    }

    public function trigger()
    {
        $this->getAgentGoldRatio();
        $gold_total = $this->result['gold'] * $this->ratio / 100;
        $openid = Member::getOpenId($this->uid);
        $member = Member::getMemberInfoById($this->uid);

        $temp_id = \Setting::get('plugin.micro')['micro_agent_gold'];
        $msg = '';
        if ($temp_id) {
            $params = [
                ['name' => '昵称', 'value' => $member->nickname],
                ['name' => '时间', 'value' => date('Y-m-d H:i:s', time())],
                ['name' => '金币数量', 'value' => $gold_total],
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
        }
        $data = [
            'member_id'     => $this->uid,
            'gold'          => intval($gold_total),
            'openid'        => $openid,
            'msg'           => $msg,
            'template_id'   => MessageTemp::$template_id
        ];
        HandleService::trigger($data);
    }
}