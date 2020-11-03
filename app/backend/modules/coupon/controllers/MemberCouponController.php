<?php

namespace app\backend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\coupon\CouponSlideShow;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;
use Yunshop\Hotel\common\models\CouponHotel;

/*
 * fixBy-wk-20201103 会员优惠券后台管理
 * */

class MemberCouponController extends BaseController
{
    //"优惠券中心"的优惠券
    const IS_AVAILABLE = 1; //可领取
    const ALREADY_GOT = 2; //已经领取
    const EXHAUST = 3; //已经被抢光

    //"个人拥有的优惠券"的状态
    const NOT_USED = 1; //未使用
    const OVERDUE = 2; //优惠券已经过期
    const IS_USED = 3; //已经使用

    const NO_LIMIT = -1; //没有限制 (比如对会员等级没有限制, 对领取总数没有限制)

    const TEMPLATEID = 'OPENTM200605630'; //成功发放优惠券时, 发送的模板消息的 ID

    /**
     * 获取用户所拥有的优惠券的数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function couponsOfMember()
    {
        $uid = \YunShop::request()->member_id;
        $pageSize = 30;

        $coupons = MemberCoupon::getCouponsOfMember($uid)->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($coupons['total'], $coupons['current_page'], $coupons['per_page']);

        //添加 "是否可用" & "是否已经使用" & "是否过期" 的标识
        $now = strtotime('now');
        foreach ($coupons['data'] as $k => $v) {
            if ($v['used'] == MemberCoupon::USED) { //已使用
                $coupons['data'][$k]['api_status'] = self::IS_USED;
            } elseif ($v['used'] == MemberCoupon::NOT_USED) { //未使用
                if ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_SINCE_RECEIVE) { //时间限制类型是"领取后几天有效"
                    $end = strtotime($v['get_time']) + $v['belongs_to_coupon']['time_days'] * 86400;
                    if ($now < $end) { //优惠券在有效期内
                        $coupons['data'][$k]['api_status'] = self::NOT_USED;
                        $coupons['data'][$k]['start'] = substr($v['get_time'], 0, 10); //前端需要起止时间
                        $coupons['data'][$k]['end'] = date('Y-m-d', $end); //前端需要起止时间

                    } else { //优惠券在有效期外
                        $coupons['data'][$k]['api_status'] = self::OVERDUE;
                    }

                } elseif ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_DATE_TIME_RANGE) { //时间限制类型是"时间范围"
                    if (($now > $v['belongs_to_coupon']['time_end'])) { //优惠券在有效期外
                        $coupons['data'][$k]['api_status'] = self::OVERDUE;
                        $coupons['data'][$k]['start'] = $coupons['data'][$k]['time_start']; //为了和前面保持一致
                        $coupons['data'][$k]['end'] = $coupons['data'][$k]['time_end']; //为了和前面保持一致
                    } else { //优惠券在有效期内
                        $coupons['data'][$k]['api_status'] = self::NOT_USED;
                    }
                }
            } else {
                $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
            }

        }

        return view('coupon.coupons-member', [
            'list' => $coupons['data'],
            'pager' => $pager,
            'total' => $coupons['total'],
        ])->render();
    }

    //用户删除其拥有的优惠券
    public function delete()
    {
        $id = \YunShop::request()->id;
        if (!$id) {
            $this->error('请传入正确参数.');
        }

        $model = MemberCoupon::find($id);
        if (!$model) {
            $this->error('找不到记录', '');
        }

        $res = $model->update(['is_member_deleted' => 1]);
        if ($res) {
            return $this->message('删除优惠券成功');
        } else {
            return $this->message('删除优惠券失败', '', 'error');
        }
    }
}
