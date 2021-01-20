<?php


namespace Yunshop\VideoDemand\Listener;

use app\common\events\payment\ChargeComplatedEvent;
use app\common\models\Member;
use app\common\facades\Setting;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\VideoDemand\models\CourseGoodsModel;
use Yunshop\VideoDemand\models\LecturerModel;
use Yunshop\VideoDemand\models\LecturerRewardLogModel;
use Yunshop\VideoDemand\models\RewardLogModel;
use Yunshop\VideoDemand\services\MessageService;

class ChargeComplatedListener
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(ChargeComplatedEvent::class, function ($event) {


            $chargeData = $event->getChargeData();
            $order_sn = $chargeData['order_sn'];

            if (substr($order_sn, 0, 2) != 'DS') {
                return;
            }

            $orderPay = RewardLogModel::where('order_sn', $order_sn)->first();

            if (isset($chargeData['unit']) && $chargeData['unit'] == 'fen') {
                $orderPay->amount = $orderPay->amount * 100;
            }

            if (bccomp($orderPay->amount, $chargeData['total_fee'], 2) == 0) {
                \Log::debug('更新订单状态');
                $data = [
                    'pay_status' => 1,
                ];

                RewardLogModel::uniacid()
                    ->where('order_sn', $order_sn)
                    ->update($data);

                if (is_null($this->hasLecturerRewardLog($order_sn))) {
                    $this->addLecturerRewardLog($order_sn, $orderPay->member_id);
                }
            }
        });

    }

    public function addLecturerRewardLog($orderSn, $member_id = 0)
    {
        $set = Setting::get('plugin.video_demand');
        $rewardLog = RewardLogModel::getRewardLogByOrderSn($orderSn)->first();

        //打赏原价
        $order_price = $rewardLog->amount;

        //打赏平台分红金额
        $amount = $rewardLog->amount;

        if ($amount <= 0) {
            return;
        }
        if (isset($set['reward_pr']) && $set['reward_pr'] > 0) {
            $deductions = $amount * ($set['reward_pr'] / 100);

            $amount = max($amount - $deductions, 0);
        }

        $courseGoods = CourseGoodsModel::getModel($rewardLog->goods_id);

        $data = [
            'uniacid' => $rewardLog->uniacid,
            'member_id' => $member_id,
            'lecturer_id' => $courseGoods->lecturer_id,
            'course_id' => $courseGoods->id,
            'order_sn' => $orderSn,
            'order_price' => $order_price,
            'reward_type' => 1,
            'amount' => $amount,
            'status' => 0,
            'settle_days' => $set['settle_days'] ? $set['settle_days'] : 0,
            'created_at' => time(),
            'updated_at' => time(),
        ];

        LecturerRewardLogModel::insert($data);

        $this->notice($data);
    }

    private function hasLecturerRewardLog($order_sn)
    {
        return LecturerRewardLogModel::hasLecturerRewardLog($order_sn)->first();
    }

    public function notice($data)
    {
        $lecturer = LecturerModel::find($data['lecturer_id']);
        $member = Member::getMemberByUid($lecturer['member_id'])->with('hasOneFans')->first();
        $messageData = [
            'amount' => $data['amount'],
        ];
        MessageService::reward($messageData, $member->hasOneFans);
    }
}
