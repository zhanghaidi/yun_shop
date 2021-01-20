<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/27
 * Time: 15:34
 */

namespace Yunshop\GroupPurchase\listeners;

use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\Order;
use app\common\services\finance\PointService;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\GroupPurchase\models\GroupPurchase;
use Yunshop\GroupPurchase\models\PurchaseOrders;
use Yunshop\GroupPurchase\models\Recommender;

class OrderReceiveListener
{
    use DispatchesJobs;

    public $event;
    public $order;
    public $set;
    public $amount = 0;
    public $order_price;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderReceivedEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderReceivedEvent $event)
    {
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);

        if ($this->order->plugin_id != PurchaseOrders::ORDER_PLUGIN_ID) {
            return;
        }

        //赠送会员积分
        $set_data = json_decode(GroupPurchase::getSettingData()['member_award_point'],true);
        if ($set_data['member_award'] == 1) {
            self::giveMemberPoint($set_data);
        }

        //赠送爱心值
        $set_love = json_decode(GroupPurchase::getSettingData()['plugins'],true);
        if ($set_love['love']['award'] == 1) {
            self::giveMemberLove($set_love);
        }

        //订单消费满额赠送
        self::giveFullReturn();
    }

    public function text($data)
    {
        $this->order = Order::find($data['id']);

    }

    /**
     * 赠送会员积分
     */
    public function giveMemberPoint($set_data)
    {
        $point = $this->order->price * $set_data['award_point'] / 100;
        $point_1 = $this->order->price * $set_data['award_point_1'] / 100;
        $point_2 = $this->order->price * $set_data['award_point_2'] / 100;
        if ($point > 0) {
            $point_data = [
                'point_income_type' => PointService::POINT_INCOME_GET,
                'point_mode'        => PointService::POINT_MODE_ORDER,
                'member_id'         => $this->order->uid,
                'point'             => $point,
                'remark'            => '拼团奖励积分'
            ];
            $point_service = new PointService($point_data);
            $point_model = $point_service->changePoint();
            if ($point_model) {
                if ($point_1 > 0) {
                    self::giveMemberPoint_1($this->order->uid,$point_1);
                }
                if ($point_2 > 0) {
                    self::giveMemberPoint_2($this->order->uid,$point_2);
                }
            }
            return true;
        } else {
            return;
        }
    }

    /**
     * 赠送上级会员积分
     */
    public function giveMemberPoint_1($uid,$point_1)
    {
        $recommend_data = Recommender::getMyReferral($uid);
        if (!empty($recommend_data['uid'])) {
            $point_data = [
                'point_income_type' => PointService::POINT_INCOME_GET,
                'point_mode' => PointService::POINT_MODE_ORDER,
                'member_id' => $recommend_data['uid'],
                'point' => $point_1,
                'remark' => '拼团奖励积分'
            ];
            $point_service = new PointService($point_data);
            $point_model = $point_service->changePoint();
        }
        return $point_model;
    }

    /**
     * 赠送上上级会员积分
     */
    public function giveMemberPoint_2($uid,$point_2)
    {
        $recommend_data = Recommender::getMyReferral($uid);
        $recommend_data_2 = Recommender::getMyReferral($recommend_data['uid']);
        if (!empty($recommend_data_2['uid'])) {
            $point_data = [
                'point_income_type' => PointService::POINT_INCOME_GET,
                'point_mode' => PointService::POINT_MODE_ORDER,
                'member_id' => $recommend_data_2['uid'],
                'point' => $point_2,
                'remark' => '拼团奖励积分'
            ];
            $point_service = new PointService($point_data);
            $point_model = $point_service->changePoint();
        }
        return $point_model;
    }

    /**
     * 赠送会员爱心值
     */
    public function giveMemberLove($set_love)
    {
        $res = app('plugins')->isEnabled('love');
        if ($res) {
            $change_value = $this->order->price * $set_love['love']['deduction_proportion'] / 100;
            $change_value_1 = $this->order->price * $set_love['love']['parent_award_proportion'] / 100;
            $change_value_2 = $this->order->price * $set_love['love']['second_award_proportion'] / 100;
            if ($change_value <= 0) {
                return;
            }
            $love_data = [
                'member_id'     => $this->order->uid,
                'change_value'  => $change_value,
                'operator'      => 'group-purchase',
                'operator_id'   => $this->order->id,
                'remark'        => '拼团奖励爱心值[' . $change_value .']',
                'relation'      => $this->order->order_sn
            ];
            (new \Yunshop\Love\Common\Services\LoveChangeService(\Yunshop\Love\Common\Services\SetService::getAwardType()))->award($love_data);
            if ($change_value_1 > 0) {
                self::giveMemberLove_1($change_value_1);
            }
            if ($change_value_2 > 0) {
                self::giveMemberLove_2($change_value_2);
            }
        }
    }

    /**
     * 赠送上级会员爱心值
     */
    public function giveMemberLove_1($change_value_1)
    {
        $recommend_data = Recommender::getMyReferral($this->order->uid);
        if (!empty($recommend_data['uid'])) {
            $love_data = [
                'member_id'     => $recommend_data['uid'],
                'change_value'  => $change_value_1,
                'operator'      => 'group-purchase',
                'operator_id'   => $this->order->id,
                'remark'        => '拼团奖励爱心值[' . $change_value_1 .']',
                'relation'      => $this->order->order_sn
            ];
            (new \Yunshop\Love\Common\Services\LoveChangeService(\Yunshop\Love\Common\Services\SetService::getAwardType()))->award($love_data);
        }
    }

    /**
     * 赠送上上级会员爱心值
     */
    public function giveMemberLove_2($change_value_2)
    {
        $recommend_data = Recommender::getMyReferral($this->order->uid);
        $recommend_data_2 = Recommender::getMyReferral($recommend_data['uid']);
        if (!empty($recommend_data_2['uid'])) {
            $love_data = [
                'member_id'     => $recommend_data_2['uid'],
                'change_value'  => $change_value_2,
                'operator'      => 'group-purchase',
                'operator_id'   => $this->order->id,
                'remark'        => '拼团奖励爱心值[' . $change_value_2 .']',
                'relation'      => $this->order->order_sn
            ];
            (new \Yunshop\Love\Common\Services\LoveChangeService(\Yunshop\Love\Common\Services\SetService::getAwardType()))->award($love_data);
        }
    }

    /**
     * 消费满额赠送
     */
    public function giveFullReturn()
    {
        if (app('plugins')->isEnabled('full-return')) {
            // todo 如果没保存过设置不进行队列处理
            if (!$this->order->getSetting('plugin.full-return')) {
                return;
            }
            $this->set = $this->order->getSetting('plugin.full-return');
            // todo 没开启返回
            if (!$this->set['is_open_return']) {
                return;
            }
            // todo 如果实付金额为0 不处理
            if ($this->order->price == 0) {
                return;
            }
            // todo 拼团
            if (!GroupPurchase::getProfitData()['full_return']['is_open']) {
                return;
            }

            DB::transaction(function () {
                self::addConsumeLog();
            });
        } else {
            return;
        }
    }

    /**
     * @name 添加消费记录
     * @author
     */
    private function addConsumeLog()
    {
        $consume_model = \Yunshop\FullReturn\common\models\ConsumeLog::getConsumeLogByUid($this->order->uid)->first();
        if ($consume_model) {
            $consume_model->consume_total += $this->order->price;
            $consume_model->consume_surplus += $this->order->price;
        } else {
            $consume_model = new \Yunshop\FullReturn\common\models\ConsumeLog();
            $consume_model->fill(self::getConsumeData());
        }
        $consume_model->save();
        $estimate_get_right = intval($consume_model->consume_surplus / $this->set['return_unit_price']);

        // todo 消费通知
        if ($estimate_get_right > 0) {
            $surplus_consume = $consume_model->consume_surplus - $estimate_get_right * $this->set['return_unit_price'];
        } else {
            $surplus_consume = $consume_model->consume_surplus;
        }
        if ($surplus_consume > 0) {
            \Yunshop\FullReturn\common\services\MessageService::consumeMessage($this->order->uid, $surplus_consume, \YunShop::app()->uniacid);
        }
        $give_ratio = $this->set['give_ratio'];

        if ($give_ratio !== '0' && empty($give_ratio)) {
            $give_ratio = 100;
        }
        if ($give_ratio === '0') {
            return;
        }

        if ($estimate_get_right > 0) {
            // 插入消费记录表
            $estimate_price = $estimate_get_right * $this->set['return_unit_price'];
            $consume_surplus = $consume_model->consume_surplus - $estimate_price;
            $consume_model->consume_surplus = $consume_surplus;
            $consume_model->save();

            // 权益限制判断
            $member_have_queue_num = \Yunshop\FullReturn\common\models\Queue::getMemberHaveQueueNum($this->order->uid, \Yunshop\FullReturn\common\models\Queue::NOT_FROZEN);
            if ($member_have_queue_num >= $this->set['limit']) {
                //插入冻结
                self::addQueue($consume_model, $estimate_price, $estimate_get_right, \Yunshop\FullReturn\common\models\Queue::IS_FROZEN);
            } else {
                $limit = $this->set['limit'] - $member_have_queue_num;
                if ($limit >= $estimate_get_right) {
                    //插入队列
                    self::addQueue($consume_model, $estimate_price, $estimate_get_right, \Yunshop\FullReturn\common\models\Queue::NOT_FROZEN);
                } else {
                    $frozen_limit = $estimate_get_right - $limit;
                    //分解
                    //limit 插入队列
                    $limit_estimate_price = $limit * $this->set['return_unit_price'];
                    self::decomposeAddQueue($consume_model, $limit_estimate_price, $limit, \Yunshop\FullReturn\common\models\Queue::NOT_FROZEN);
                    //frozen_limit 插入冻结
                    $frozen_estimate_price = $frozen_limit * $this->set['return_unit_price'];
                    self::decomposeAddQueue($consume_model, $frozen_estimate_price, $frozen_limit, \Yunshop\FullReturn\common\models\Queue::IS_FROZEN);
                }
            }
        }
    }

    private function decomposeAddQueue($consume_model, $estimate_price, $estimate_get_right, $is_frozen = \Yunshop\FullReturn\common\models\Queue::NOT_FROZEN)
    {
        $queue_model = \Yunshop\FullReturn\common\models\Queue::getQueueByUidAndByUnitAndRatio($consume_model->uid, $this->set['return_unit_price'], $this->set['give_ratio'], $is_frozen)->first();
        if ($queue_model) {
            $queue_model->total += $estimate_get_right;
            $queue_model->amount_total += $estimate_price;
            $queue_model->amount_surplus += $estimate_price;
            $queue_model->discount_before_amount += $estimate_price;
            $queue_model->status = \Yunshop\FullReturn\common\models\Queue::STATUS_FALSE;
            $queue_model->is_frozen = $is_frozen;
        } else {
            $give_ratio = $this->set['give_ratio'];
            if ($give_ratio !== '0' && empty($give_ratio)) {
                $give_ratio = 100;
            }
            $time = time();
            $queue_model = new \Yunshop\FullReturn\common\models\Queue();
            $queue_model->fill([
                'uniacid'                   => $consume_model->uniacid,
                'uid'                       => $consume_model->uid,
                'unit_price'                => $this->set['return_unit_price'],
                'total'                     => $estimate_get_right,
                'amount_total'              => $estimate_price,
                'amount_finish'             => 0,
                'amount_surplus'            => $estimate_price,
                'status'                    => \Yunshop\FullReturn\common\models\Queue::STATUS_FALSE,
                'last_amount'               => 0,
                'last_at'                   => $time,
                'give_ratio'                => $give_ratio,
                'discount_before_amount'    => $estimate_price,
                'is_frozen'                 => $is_frozen
            ]);
        }
        $queue_model->save();

        $remark = '下单是放入队列';
        if ($is_frozen == \Yunshop\FullReturn\common\models\Queue::IS_FROZEN) {
            $remark = '下单是放入冻结';
        }
        \Yunshop\FullReturn\common\services\OperationLogService::add($queue_model, 0, self::class, 200, $remark);

        //todo 发送获得队列消息
        \Yunshop\FullReturn\common\services\MessageService::queueMessage($queue_model->uid, $estimate_price, $queue_model->uniacid);
    }

    private function addQueue($consume_model, $estimate_price, $estimate_get_right, $is_frozen = \Yunshop\FullReturn\common\models\Queue::NOT_FROZEN)
    {
        $queue_model = \Yunshop\FullReturn\common\models\Queue::getQueueByUidAndByUnitAndRatio($consume_model->uid, $this->set['return_unit_price'], $this->set['give_ratio'], $is_frozen)->first();
        $right = ceil($estimate_price / $this->set['return_unit_price']);
        if ($queue_model) {
            $queue_model->total += $right;
            $queue_model->amount_total += $estimate_price;
            $queue_model->amount_surplus += $estimate_price;
            $queue_model->discount_before_amount += $estimate_price;
            $queue_model->status = \Yunshop\FullReturn\common\models\Queue::STATUS_FALSE;
            $queue_model->is_frozen = $is_frozen;
        } else {
            $queue_model = new \Yunshop\FullReturn\common\models\Queue();
            $queue_model->fill(self::getQueueData($consume_model, $this->set['return_unit_price'], $estimate_get_right, $is_frozen));
        }
        $queue_model->save();

        $remark = '下单是放入队列';
        if ($is_frozen == \Yunshop\FullReturn\common\models\Queue::IS_FROZEN) {
            $remark = '下单是放入冻结';
        }
        \Yunshop\FullReturn\common\services\OperationLogService::add($queue_model, 0, self::class, 200, $remark);

        //todo 发送获得队列消息
        \Yunshop\FullReturn\common\services\MessageService::queueMessage($queue_model->uid, $estimate_price, $queue_model->uniacid);
    }

    private function getQueueData($model, $unit_price, $estimate_get_right, $is_frozen = \Yunshop\FullReturn\common\models\Queue::NOT_FROZEN)
    {
        $give_ratio = $this->set['give_ratio'];
        if ($give_ratio !== '0' && empty($give_ratio)) {
            $give_ratio = 100;
        }
        $time = time();
        $amount_total = $estimate_get_right * $unit_price;
        $estimate_get_right = ceil($amount_total / $this->set['return_unit_price']);
        return [
            'uniacid'                   => $model->uniacid,
            'uid'                       => $model->uid,
            'unit_price'                => $unit_price,
            'total'                     => $estimate_get_right,
            'amount_total'              => $amount_total,
            'amount_finish'             => 0,
            'amount_surplus'            => $amount_total,
            'status'                    => \Yunshop\FullReturn\common\models\Queue::STATUS_FALSE,
            'last_amount'               => 0,
            'last_at'                   => $time,
            'give_ratio'                => $give_ratio,
            'discount_before_amount'    => $amount_total,
            'is_frozen'                 => $is_frozen
        ];
    }
    /**
     * @name 获取消费数组
     * @author
     * @return array
     */
    private function getConsumeData()
    {
        return [
            'uniacid'           => \YunShop::app()->uniacid,
            'uid'               => $this->order->uid,
            'consume_total'     => $this->order->price,
            'consume_surplus'   => $this->order->price
        ];
    }

    private function getGiveRatio()
    {
        $give_ratio = $this->set['give_ratio'];
        if ($give_ratio !== '0' && empty($give_ratio)) {
            $give_ratio = 100;
        }
        return $give_ratio;
    }
}