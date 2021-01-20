<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/18
 * Time: 上午11:42
 */

namespace Yunshop\VideoDemand\api;


use app\common\components\ApiController;
use app\common\events\payment\ChargeComplatedEvent;
use app\common\facades\Setting;
use app\common\services\PayFactory;
use Yunshop\VideoDemand\models\CourseGoodsModel;
use Yunshop\VideoDemand\models\RewardLogModel;

class LecturerRewardController extends ApiController
{
    public $set;
    public $uid;

    public function __construct()
    {
        parent::__construct();

        $this->set = Setting::get('plugin.video_demand');
        $this->uid = \YunShop::app()->getMemberId();
    }


    public function runRewardPay()
    {
        // plugin.video-demand.api.lecturer-reward.run-reward-pay
        /**
         * amount 支付金额
         * pay_method 支付类型
         * goods_id 商品ID
         */
        $amount = \YunShop::request()->amount;
        $payMethod = \YunShop::request()->pay_method;
        $goodsId = \YunShop::request()->goods_id;
        $courseGoods = CourseGoodsModel::getModel($goodsId);
        if (!$courseGoods->goods_id && !$courseGoods->lecturer_id) {
            return $this->errorJson('该商品不能打赏！');
        }

        $member_id = \YunShop::app()->getMemberId();

        if ($amount <= 0) {
            return $this->errorJson('支付金额不正确！', $amount);
        }

        $order_sn = $this->addPayLog($member_id, $amount, $payMethod, $goodsId, $courseGoods->lecturer_id);

        if ($order_sn) {
            $payRequest = $this->getRewardPay($amount, $payMethod, $order_sn);
        }
        if ($payRequest) {
            if (is_array($payRequest)) {
                if ($payRequest['js'] && $payRequest['config']) {
                    $payRequest['js'] = json_decode($payRequest['js'], 1);
                }
            }
            if (is_bool($payRequest[0]) && $payMethod == 3) {
                event(new ChargeComplatedEvent([
                    'order_sn' => $order_sn,
                    'pay_sn' => '',
                    'total_fee' => $amount
                ]));
            }

            return $this->successJson('支付成功', $payRequest);
        }
        return $this->errorJson('支付失败', $payRequest);
    }

    public function getRewardPay($amount, $payMethod, $orderNo)
    {
        /**
         *
         * @param $subject 名称
         * @param $body 详情
         * @param $amount 金额
         * @param $order_no 订单号
         * @param $extra 附加数据
         * @return strin5
         */
        $data = [
            'subject' => '视频点播打赏-支付',
            'body' => '视频点播打赏:' .\YunShop::app()->uniacid,
            'amount' => $amount,
            'order_no' => $orderNo,
            'extra' => ['type' => 1],
        ];

        return PayFactory::pay($payMethod, $data);
    }

    public function addPayLog($memberId, $amount, $payMethod, $goodsId, $lecturerId)
    {

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $memberId,
            'goods_id' => $goodsId,
            'lecturer_id' => $lecturerId,
            'amount' => $amount,
            'order_sn' => RewardLogModel::createOrderSn('DS', 'order_sn'),
            'pay_method' => $payMethod,
            'pay_status' => 0,
            'created_at' => time(),
            'updated_at' => time(),
        ];

        if (RewardLogModel::insert($data)) {
            return $data['order_sn'];
        }
        return '';
    }


}