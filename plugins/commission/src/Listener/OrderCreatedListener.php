<?php


namespace Yunshop\Commission\Listener;

use app\common\facades\Setting;
use app\Jobs\OrderBonusJob;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\CommissionOrderGoods;
use Yunshop\Commission\services\AgentService;
use Yunshop\Commission\services\CommissionOrderService;
use Yunshop\Commission\services\ExpandDividendService;
use Yunshop\Commission\services\MessageService;
use Yunshop\Commission\services\OrderCreatedService;

class OrderCreatedListener
{
    use DispatchesJobs;

    public $model;
    public $order;

    public $config;
    public $set;

    public $requestAgents;
    public $level_weight = 0;
    public $buyer_uid = 0;

    /**
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(\app\common\events\order\AfterOrderCreatedEvent::class, function ($event) {
            date_default_timezone_set("PRC");
            $orderModel = $event->getOrderModel();
            $result = $this->handler($orderModel);
            \Log::debug('订单分销' . $orderModel->id . ':' . $result);
        });
    }

    public function handler($orderModel)
    {
        $this->set = Setting::get('plugin.commission');
        //定制版设置
        $expand_set = Setting::get('plugin.commission_expand');
        $this->config = \app\common\modules\shop\ShopConfig::current()->get('plugin.commission');

        $this->model = $orderModel;
//        $this->order = Order::find($this->model->id);
        //订单model （不用再find一次了）
        $this->order = $orderModel;

        //验证分销插件是否开启
        if (!$this->set['is_commission']) {
            \Log::debug('分销执行分销设置:' . json_encode($this->set, 256));

            /*Operation::create([
                'uniacid' => $this->order->uniacid,
                'order_id' => $this->order->id,
                'uid' => 0,
                'buy_uid' => $this->order->uid,
                'level_id' => 0,
                'ratio' => 0,
                'content' => '未开启分销'
            ]);*/

            return '未开启分销';
        }
        if ($expand_set['is_expand']) {
            (new ExpandDividendService($this->order, $expand_set, $this->set))->getAgent();
            return '开启分销定制版分红';
        }

        // 购买者身份
        $buyer = Agents::getAgentByMemberId($this->order->uid)->first();
        if ($buyer && $buyer->agentLevel->level) {
            // 额外分红需要
            $this->level_weight = $buyer->agentLevel->level;
            $this->buyer_uid = $this->order->uid;
        }

        //获取上级关系链
        $this->requestAgents = OrderCreatedService::getParentAgents($this->model->uid, $this->set['self_buy']);
        \Log::debug('订单分销' . $orderModel->id . '关系链', $this->requestAgents);

        //确认分销商层级
        $agents = AgentService::getParentAgents($this->requestAgents->toArray(), $this->set);
        \Log::debug('订单分销' . $orderModel->id . '层级', $agents);

        if (!isset($agents['first_level'])) {
            \Log::debug('订单分销没有上级',print_r($orderModel->id,true));
            return '没有上级';
        }
        //分销订单
        $this->commissionOrdersData($agents, $orderModel->id);

        //预计佣金
        $totalCommission = CommissionOrderService::expectedDividends($this->order, $this->set);
        \Log::debug('订单分销' . $orderModel->id . '金额', $totalCommission);
        // 订单插件分红记录
        (new OrderBonusJob('yz_commission_order', 'commission', 'ordertable_id', 'id', 'commission', $this->order, $totalCommission))->handle();

        return '完成';
    }

    /**
     * @param $agents
     * @param $order_id
     */
    public function commissionOrdersData($agents, $order_id)
    {
        // 断层不进行额外分红
        $is_additiona_commission = false;
        //分销商分别添加 分销订单
        foreach ($agents as $level => $agent) {
            if (!$agent) {
                \Log::debug('分销异常'.$order_id,print_r($level,true));
                continue;
            }

            if (empty($agent['agent']) || $agent['agent']['is_black']) {
                \Log::debug('订单分销不是分销商或者被拉黑'.$order_id,print_r($agent,true));
//                $empty_agent = empty($agent['agent']);
                /*Operation::create([
                    'uniacid' => $this->order->uniacid,
                    'order_id' => $this->order->id,
                    'uid' => $agent['agent']['member_id'],
                    'buy_uid' => $this->order->uid,
                    'level_id' => 0,
                    'ratio' => 0,
                    'content' => "不是分销商或者被拉黑agent[{$empty_agent}]black[{$agent['agent']['is_black']}]"
                ]);*/

                continue;
            }

            //该分销商所在层级
            $hierarchy = CommissionOrderService::getHierarchy($level);
            $agent['agent']['hierarchy'] = $level;

            //获取佣金 计算金额 计算公式 佣金比例 分销订单商品等数据
            $commission = CommissionOrderService::getCommission($this->order, $agent['agent'], $this->set);

            if (1 || $commission['commission'] > 0) {
                $this->addCommissionOrder($commission, $agent, $hierarchy, $level);
            } else {
                \Log::debug('订单分销没有佣金'.$order_id,print_r($agent,true));
                /*Operation::create([
                    'uniacid' => $this->order->uniacid,
                    'order_id' => $this->order->id,
                    'uid' => $agent['agent']['member_id'],
                    'buy_uid' => $this->order->uid,
                    'level_id' => 0,
                    'ratio' => 0,
                    'content' => "没有分销金额"
                ]);*/
            }

            // 额外分红
            if (!$is_additiona_commission) {
                // 不包括自己 不管是不是开启了内购
                if ($agent['agent']['agent_level']['level'] >= $this->level_weight && $agent['member_id'] != $this->buyer_uid) {
                    $is_additiona_commission = true;
                    $additiona_commission = CommissionOrderService::getAdditionalCommission($this->order, $agent['agent'], $this->set);

                    if ($additiona_commission['commission'] > 0) {
                        $this->addCommissionOrder($additiona_commission, $agent, 0, $level);
                    }
                }
            }
        }
    }

    /**
     * @param $commission
     * @param $agent
     * @param $hierarchy
     * @param $level
     */
    public function addCommissionOrder($commission, $agent, $hierarchy, $level)
    {
        //分销订单数据
        $orderData = [
            'uniacid' => \YunShop::app()->uniacid,
            'ordertable_type' => $this->config['order_class'],
            'ordertable_id' => $this->model->id,
            'buy_id' => $this->model->uid,
            'member_id' => $agent['member_id'],
            'hierarchy' => $hierarchy,//分销层级
            'commission_amount' => $commission['commission_amount'],// 计算金额,
            'formula' => $commission['formula'],// 计算公式
            'commission_rate' => $commission['commission_rate'],// 佣金比例
            'commission' => $commission['commission'],// 佣金
            'status' => 0,
            'settle_days' => $this->set['settle_days'],
            'created_at' => time(),
        ];
        $exist = CommissionOrder::where([
            'member_id' => $orderData['member_id'],
            'ordertable_id' => $orderData['ordertable_id'],
            'hierarchy' => $orderData['hierarchy'],
            'commission_amount' => $orderData['commission_amount'],
            'commission' => $orderData['commission'],
        ])->count();
        if ($exist) {
            \Log::debug("订单{$orderData['ordertable_id']}:", "分销商{$orderData['member_id']}的分红记录已存在");
            return;
        }
        //todo 防止多队列支付先走
        $order = \app\common\models\Order::find($this->order->id);
        if ($this->set['settlement_event'] == 1 && $order->status >= 1) {
            $orderData['recrive_at'] = strtotime($order->pay_time);
            $orderData['status'] = 1;
        }
        if ($this->set['settlement_event'] == 0 && $order->status == 3) {
            $orderData['recrive_at'] = strtotime($order->finish_time);
            $orderData['status'] = 1;
        }
        //添加分销订单数据 生成ID
        $commissionOrderIds = CommissionOrder::insertGetId($orderData);

        $this->notice($commission, $agent, $hierarchy, $level);

        $this->addCommissionOrderGoods($commission, $commissionOrderIds);

    }

    /**
     * @param $agent
     * @param $commission
     * @param $hierarchy
     * @param $level
     */
    public function notice($commission, $agent, $hierarchy, $level)
    {
        $noticeData = [
            'order' => $this->order,
            'goods' => $this->model->hasManyOrderGoods->toArray(),
            'agent' => $agent['has_one_fans'],
            'buy' => $this->requestAgents->hasOneFans,
            'commission' => $commission['commission'],
            'hierarchy' => $hierarchy
        ];

        if ($this->set['self_buy'] && $level != "first_level") {
            MessageService::createdOrder($noticeData);
        } elseif (!$this->set['self_buy']) {
            MessageService::createdOrder($noticeData);
        }
    }

    /**
     * @param $commission
     * @param $commissionOrderIds
     */
    public function addCommissionOrderGoods($commission, $commissionOrderIds)
    {
        //分销订单商品数据
        foreach ($commission['orderGoods'] as $orderGood) {
            $orderGoodsData[] = [
                'commission_order_id' => $commissionOrderIds,
                'name' => $orderGood->goods['name'],
                'thumb' => $orderGood->goods['thumb'],
                'has_commission' => $orderGood->commissionGoods['has_commission'],
                'commission_rate' => $orderGood->commissionGoods['commission_rate'],
                'commission_pay' => $orderGood->commissionGoods['commission_pay']
            ];
        }

        if (!empty($orderGoodsData)) {
            //添加分销订单商品数据
            CommissionOrderGoods::insert($orderGoodsData);
        }
    }


    /**
     * 修复生成商城订单时分销订单创建失败
     *
     * @param $OrderModel
     */
    public function fixCreatedOrder($orderModel)
    {
        $this->handler($orderModel);
    }
}
