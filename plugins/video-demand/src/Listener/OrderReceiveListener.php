<?php


namespace Yunshop\VideoDemand\Listener;

use app\common\facades\Setting;
use app\common\models\Member;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\VideoDemand\models\CourseGoodsModel;
use Yunshop\VideoDemand\models\LecturerModel;
use Yunshop\VideoDemand\models\LecturerRewardLogModel;
use Yunshop\VideoDemand\models\MemberCourseModel;
use Yunshop\VideoDemand\services\MessageService;

class OrderReceiveListener
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(\app\common\events\order\AfterOrderReceivedEvent::class, function ($event) {
            //订单model
            $model = $event->getOrderModel();

            $order_goods = $model->hasManyOrderGoods;

            $order_member = $model->belongsToMember;

            /**
             *  增加购买记录
             */
            $this->addMemberCourse($model);

            /**
             *  讲师分红
             */
            $this->addLecturerRewardLog($model);

        });
    }

    /**
     * @param $model
     * @return mixed
     */
    public function addMemberCourse($model)
    {
        $orderGoods = $model->hasManyOrderGoods[0];
        $orderMember = $model->belongsToMember;
        $orderSn = $model->order_sn;

        if ($orderGoods['goods_id']) {
            $course = CourseGoodsModel::checkCourse($orderGoods['goods_id'], 1)->first();

            if (is_null($course)) {
                return;
            }
        }

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $orderMember->uid,
            'goods_id' => $orderGoods['goods_id'],
            'order_sn' => $orderSn,
            'created_at' => time(),
            'updated_at' => time(),
        ];
        return MemberCourseModel::insert($data);
    }

    /**
     * @param $model
     */
    public function addLecturerRewardLog($model)
    {
        $set = Setting::get('plugin.video_demand');
        $orderGoods = $model->hasManyOrderGoods[0];
        $orderMember = $model->belongsToMember;
        $orderSn = $model->order_sn;
        $amount = $orderGoods['payment_amount'] - $orderGoods['goods_cost_price'];

        if ($amount <= 0) {
            return;
        }

        if ($orderGoods['goods_id']) {
            $course = CourseGoodsModel::checkCourse($orderGoods['goods_id'], 1)->first();

            if (is_null($course)) {
                return;
            }
        }

        $courseGoods = CourseGoodsModel::getModel($orderGoods['goods_id']);


        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $orderMember->uid,
            'lecturer_id' => $courseGoods->lecturer_id,
            'course_id' => $courseGoods->id,
            'order_sn' => $orderSn,
            'order_price' => $model->price,
            'reward_type' => 0,
            'amount' => $amount,
            'status' => 0,
            'settle_days' => $set['settle_days'] ? $set['settle_days'] : 0,
            'created_at' => time(),
            'updated_at' => time(),
        ];
        LecturerRewardLogModel::insert($data);

        $this->notice($data);
    }

    public function notice($data)
    {
        $lecturer = LecturerModel::find($data['lecturer_id']);
        $member = Member::getMemberByUid($lecturer['member_id'])->with('hasOneFans')->first();
        $course = CourseGoodsModel::find($data['course_id']);

        $messageData = [
            'goods_name' => $course->goods_title,
            'order_price' => $data['order_price'],
            'amount' => $data['amount'],
        ];

        MessageService::orderReward($messageData, $member->hasOneFans);
    }

}
