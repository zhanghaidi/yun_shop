<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-06-26
 * Time: 18:27
 */

namespace Yunshop\LuckyDraw\frontend;


use app\common\models\Member;
use app\common\models\MemberShopInfo;
use app\common\models\Order;
use app\common\services\finance\PointService;
use Yunshop\Love\Common\Models\MemberLove;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\LuckyDraw\common\models\DrawActivityModel;
use app\common\components\ApiController;
use Yunshop\LuckyDraw\common\models\DrawByMemberModel;
use Yunshop\LuckyDraw\common\models\DrawPrizeModel;
use Yunshop\LuckyDraw\common\models\DrawPrizeRecordModel;
use Yunshop\LuckyDraw\common\models\DrawShareModel;
use Yunshop\LuckyDraw\common\services\DrawRechargeService;
use Yunshop\LuckyDraw\common\services\DrawRewardService;

class DrawController extends ApiController
{
    public function index()
    {
        $member_id = $this->getMemberId();
        $id = (int)request()->lotteryId;
        if (!$id) {
            return $this->errorJson('请传入正确参数！');
        }

        $activityModel = DrawActivityModel::uniacid()->find($id);

        if (!$activityModel) {
            return $this->errorJson('活动不存在或已删除！');
        }

        $my_point = Member::select('uid', 'credit1')->where('uid', $member_id)->first();

        $name_list = DrawPrizeRecordModel::with([
            'Member' => function ($q) {
                $q->select(['uid', 'mobile', 'nickname']);
            }, 'hasOneCoupon' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where('activity_id', $activityModel->id)
            ->orderBy('created_at', 'desc')
            ->limit(30)->get();

        $prize_ids = $activityModel->prize_id;

        $prizeModels = DrawPrizeModel::uniacid()->with(['hasOneCoupon' => function ($q) {
            $q->select(['id', 'name']);
        }])->whereIn('id', $prize_ids)->get();

        $prize_chances = $prizeModels->sum('chance');
        $empty_chance = 100 - $prize_chances;

        $count = $prizeModels->count();

        switch ($count) {
            case 1:
                foreach ($prizeModels as $k => $v) {
                    $prizeModels[$k]->chance = $v->chance/6;
                }
                break;
            case 2:
                foreach ($prizeModels as $k => $v) {
                    $prizeModels[$k]->chance = $v->chance/3;
                }
                break;
            case 3:
                foreach ($prizeModels as $k => $v) {
                    $prizeModels[$k]->chance = $v->chance/2;
                }
                break;
            case 4:
                foreach ($prizeModels as $k => $v) {
                    if ($k == 0 || $k == 1 || $k == 4 || $k == 5) {
                        $prizeModels[$k]->chance = $v->chance/2;
                    }
                }
                break;
            case 5:
                foreach ($prizeModels as $k => $v) {
                    if ($k == 0 || $k == 5) {
                        $prizeModels[$k]->chance = $v->chance/2;
                    }
                }
                break;
        }

        $prize_arr = [];
        foreach ($prizeModels as $k => $v) {
            $prize_arr[$k] = [
                'id' => $v->id,
                'prize' => $v->name,
                'thumb_url' => $v->thumb_url,
                'chance' => $v->chance, //概率
                'coupon' => $v->hasOneCoupon->name,
                'point' => $v->point,
                'love' => $v->love,
                'amount' => $v->amount,
                'prize_num' => $v->prize_num, //奖品数量
            ];
        }

        $empty_result = [];
        for ($i = 1; $i<= 6; $i++) {
            array_push($empty_result, [
                'id' => $i+6,
                'prize' => $activityModel->empty_prize_name,
                'thumb_url' => $activityModel->empty_prize_thumb,
                'jump_type' => $activityModel->jump_type,
                'jump_link' => $activityModel->jump_link,
                'chance' => $empty_chance/6,
            ]);
        }

        $result = [];
        $a = 0;
        $b = 0;
        $mun = count($prize_arr);
        for ($i=1; $i <= 12; $i++){
            if ($i % 2 == 0) {
                if ($a >= $mun) {
                    $a = 0;
                }
                $result[] = $prize_arr[$a];
                $a ++;
            } else {
                $result[] = $empty_result[$b];
                $b++;
                if ($b > 5) {
                    $b = 0;
                }
            }
        }

        foreach ($result as $k => &$item) {
            $item['item'] = $k;
        }

        $day_times = DrawByMemberModel::uniacid()
            ->where('activity_id', $activityModel->id)
            ->where('member_id', $member_id)
            ->whereBetween('created_at', $this->getToday())
            ->count();

        $times = DrawByMemberModel::uniacid()
            ->where('activity_id', $activityModel->id)
            ->where('member_id', $member_id)
            ->limit($activityModel->somebody_times)
            ->get()
            ->count();

        $share_day_times = DrawShareModel::uniacid()
            ->where('activity_id', $activityModel->id)
            ->where('member_id', $member_id)
            ->whereBetween('created_at', $this->getToday())
            ->first();

        $share_times = DrawShareModel::uniacid()
            ->where('activity_id', $activityModel->id)
            ->where('member_id', $member_id)
            ->first();

        if ($activityModel->partake_times == 0) {
            $surplus_time = (intval($activityModel->days_times) + intval($share_day_times->times)) - $day_times;
            if ($surplus_time <= 0) {
                $surplus_time = 0;
            }
        } else {
            $surplus_time = (intval($activityModel->somebody_times) + intval($share_times->times)) - $times;
            if ($surplus_time <= 0) {
                $surplus_time = 0;
            }
        }

        $word_setting = \Setting::get('shop.shop');
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
        }

        return $this->successJson('成功！', [
            'amount_word' => $word_setting['credit']?:'余额',
            'point_word' => $word_setting['credit1']?:'积分',
            'love_word' => $love_name?:'爱心值',
            'shop_logo' => yz_tomedia($word_setting['logo']),
            'point' => $my_point,
            'list' => $name_list,
            'activityModel' => $activityModel,
            'result_arr' => $result,
            'surplus_time' => $surplus_time,
        ]);
    }

    public function getMyRecord()
    {
        $id = (int)request()->lotteryId;
        $member_id = $this->getMemberId();
        if (!$id) {
            return $this->errorJson('请传入正确参数！');
        }

        $activityModel = DrawActivityModel::uniacid()->find($id);

        if (!$activityModel) {
            return $this->errorJson('活动不存在或已删除！');
        }

        $my_record = DrawPrizeRecordModel::whereHas('hasOneActivity', function ($q) use ($activityModel) {
            $q->where('id', $activityModel->id);
        })->with([
            'hasOneCoupon' => function ($q) {
                $q->select(['id', 'name']);
        }])->where('member_id', $member_id)
            ->paginate();

        $word_setting = \Setting::get('shop.shop');
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
        }

        return $this->successJson('ok', [
            'amount_word' => $word_setting['credit']?:'余额',
            'point_word' => $word_setting['credit1']?:'积分',
            'love_word' => $love_name?:'爱心值',
            'my_record' => $my_record,
            'activity_name' => $activityModel->name,
        ]);
    }

    public function doDraw()
    {
        $member_id = $this->getMemberId();
        $id = (int)request()->lotteryId;
        $activityModel = DrawActivityModel::uniacid()->find($id);

        if ($activityModel->countdown_time[1] < time() || $activityModel->countdown_time[0] > time()) {
            return $this->errorJson('不在活动时间内！');
        }

        $pass = $this->memberLimit($member_id, $activityModel);
        if ($pass == false) {
            return $this->errorJson('参与用户身份不正确, 没有资格');
        }

        if (!empty($activityModel->goods_id)) {
            if (empty($this->GoodsLimit($activityModel->goods_id))) {
                return $this->errorJson('请购买抽奖指定商品', [
                    'goods_id' => $activityModel->goods_id,
                ]);
            }
        }

        $member = Member::select('uid', 'credit1')->where('uid', $member_id)->first();

        if ($activityModel->type == 1) {
            if ($member->credit1 < $activityModel->use_point) {
                return $this->errorJson('用户积分不足！');
            }
        }elseif ($activityModel->type == 2) {
            if (app('plugins')->isEnabled('love')) {
                $member_love = MemberLove::select('member_id', 'usable')->where('member_id', $member_id)->first();
            }else{
                return $this->errorJson('请开启爱心值插件');
            }
            if ($member_love->usable < $activityModel->use_love) {
                return $this->errorJson('用户爱心值不足！');
            }
        }

        //今天的抽奖次数记录
        $draw_member_log_today = DrawByMemberModel::uniacid()
            ->where('activity_id', $activityModel->id)
            ->where('member_id', $member_id)
            ->whereBetween('created_at', $this->getToday())
            ->count();

        //总抽奖次数记录
        $draw_member_log = DrawByMemberModel::uniacid()
            ->where('activity_id', $activityModel->id)
            ->where('member_id', $member_id)
            ->count();

        //分享获得次数
        $share_times = DrawShareModel::uniacid()
            ->where('activity_id', $activityModel->id)
            ->where('member_id', $member_id)
            ->pluck('times');

        if (is_null($activityModel->partake_times) || $activityModel->partake_times == 0) {
            $sum_times = $activityModel->days_times + $share_times; //每天可抽奖次数
            if ($draw_member_log_today > $sum_times) {
                return $this->errorJson('您今天的抽奖次数已用完！');
            }
        } else {
            $sum_times = $activityModel->somebody_times + $share_times; //每人可抽奖次数
            if ($draw_member_log > $sum_times) {
                return $this->errorJson('您的抽奖次数已用完！');
            }
        }

        if ($activityModel->limit == 0) {
            (new DrawRewardService($member_id, $activityModel, $this->getUiniacid()))->doReward();
        }

        if ($activityModel->draw_type == 1) {
            $point_data = [
                'point_mode' => PointService::POINT_MODE_DRAW_CHARGE_DEDUCTION,
                'member_id' => $member_id,
                'point' => -$activityModel->use_point,
                'remark' => '[会员ID:'.$member_id.'参加抽奖活动:'.$activityModel->name.'使用了积分'.$activityModel->use_point.']',
                'point_income_type' => PointService::POINT_INCOME_LOSE
            ];

            $pointService = new PointService($point_data);
            $pointService->changePoint();
        } elseif($activityModel->draw_type == 2) {
            $love_name = SetService::getLoveName();
            $data = [
                'member_id' => $member_id,
                'change_value' => $activityModel->use_love,
                'operator' => 0,
                'operator_id' => 0,
                'remark' => '[会员ID:'.$member_id.'参加抽奖活动:'.$activityModel->name.'使用了'.$love_name.$activityModel->use_love.']',
                'relation' => ''
            ];

            (new LoveChangeService())->DrawUsed($data);
        }

        //添加抽奖记录
        $draw_member_log = new DrawByMemberModel();
        $log_data = [
            'uniacid' => $this->getUiniacid(),
            'member_id' => $member_id,
            'activity_id' => $activityModel->id,
            'log' => 1,
        ];
        $draw_member_log->fill($log_data);
        $draw_member_log->save();

        $prize_ids = $activityModel->prize_id;

        $prizeModels = DrawPrizeModel::uniacid()->whereIn('id', $prize_ids)->get();

        $prize_chances = $prizeModels->sum('chance');
        $empty_chance = 100 - $prize_chances;

        $count = $prizeModels->count();

        switch ($count) {
            case 1:
                foreach ($prizeModels as $k => $v) {
                    $prizeModels[$k]->chance = $v->chance/6;
                }
                break;
            case 2:
                foreach ($prizeModels as $k => $v) {
                    $prizeModels[$k]->chance = $v->chance/3;
                }
                break;
            case 3:
                foreach ($prizeModels as $k => $v) {
                    $prizeModels[$k]->chance = $v->chance/2;
                }
                break;
            case 4:
                foreach ($prizeModels as $k => $v) {
                    if ($k == 0 || $k == 1 || $k == 4 || $k == 5) {
                        $prizeModels[$k]->chance = $v->chance/2;
                    }
                }
                break;
            case 5:
                foreach ($prizeModels as $k => $v) {
                    if ($k == 0 || $k == 5) {
                        $prizeModels[$k]->chance = $v->chance/2;
                    }
                }
                break;
        }

        $prize_arr = [];
        foreach ($prizeModels as $k => $v) {
            $prize_arr[$k] = [
                'id' => $v->id,
                'prize' => $v->name,
                'thumb_url' => $v->thumb_url,
                'chance' => $v->chance, //概率
                'coupon' => $v->hasOneCoupon->name,
                'point' => $v->point,
                'love' => $v->love,
                'amount' => $v->amount,
                'prize_num' => $v->prize_num, //奖品数量
            ];
        }

        $empty_result = [];
        for ($i = 1; $i<= 6; $i++) {
            array_push($empty_result, [
                'id' => $i+6,
                'prize' => $activityModel->empty_prize_name,
                'thumb_url' => $activityModel->empty_prize_thumb,
                'jump_type' => $activityModel->jump_type,
                'jump_link' => $activityModel->jump_link,
                'empty_prize_prompt' => $activityModel->empty_prize_prompt,
                'chance' => $empty_chance/6,
            ]);
        }

        $arr = [];

        $result = [];
        $a = 0;
        $b = 0;
        $mun = count($prize_arr);
        for ($i=1; $i <= 12; $i++){
            if ($i % 2 == 0) {
                if ($a >= $mun) {
                    $a = 0;
                }
                $result[] = $prize_arr[$a];
                $a ++;
            } else {
                $result[] = $empty_result[$b];
                $b++;
                if ($b > 5) {
                    $b = 0;
                }
            }
        }

        foreach ($result as $k => &$item) {
            $item['item'] = $k;
        }

        foreach ($result as $key => $val) {
            $arr[$val['item']] = $val['chance'];
        }

        $key = $this->get_rand($arr);

        $result_data = $result[$key];

        $prize_id = 0;
        if (array_key_exists('prize_num', $result_data)) {
            $prize_id = $result_data['id'];
        }

        $prize = DrawPrizeModel::uniacid()->find($prize_id);

        if (empty($prize) || $prize->prize_num <= 0 || $result['prize'] == '空奖') {
            if ($key != 0 && $key % 2 != 0) {
                $result = $result[$key-1];
            } else {
                $result = $result_data;
            }
            if ($activityModel->limit == 1) {
                (new DrawRewardService($member_id, $activityModel, $this->getUiniacid()))->doReward();
            }
            return $this->successJson('谢谢惠顾！', $result);
        }

        //抽奖奖品减数量
        $prize->prize_num = $prize->prize_num - 1;
        $prize->save();

        //添加中奖记录
        $draw_log = new DrawPrizeRecordModel();
        $draw_log_data = [
            'uniacid' => $this->getUiniacid(),
            'member_id' => $member_id,
            'activity_id' => $activityModel->id,
            'prize_id' => $prize->id,
            'point' => $prize->point,
            'love' => $prize->love,
            'amount' => $prize->amount,
            'coupon_id' => $prize->coupon_id,
            'prize_name' => $prize->name,
        ];
        $draw_log->fill($draw_log_data);
        $draw_log->save();

        (new DrawRechargeService($prize, $member_id, $this->getUiniacid()))->chargeOfType();

        return $this->successJson('恭喜你，中奖了！',[
            'result' => $result_data,
        ]);
    }

    public function get_rand($proArr)
    {
        $proArr = collect($proArr)->map(function ($value) {
            return $value * 100;
        })->toArray();

        $result = '';

        //概率数组的总概率精度
        $proSum = array_sum($proArr);

        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }

        unset($proArr);

        return $result;
    }

    public function memberLimit($member_id, $condition)
    {
        $yz_member = MemberShopInfo::select('member_id', 'level_id', 'is_agent')->where('member_id', $member_id)->first();

        $pass = true;

        if ($condition->role_type == 1) {
            if ($condition->member_type == 1) {
                if ($yz_member->is_agent != 1) {
                    $pass = false;
                }
            }else{
                if ($condition->level_id != $yz_member->level_id) {
                    $pass = false;
                }
            }
        }

        return $pass;
    }

    public function GoodsLimit($goods_id)
    {
        $pass = Order::select('yz_order.id', 'yz_order.uid')
            ->where('status', '>=', 1)
            ->where('yz_order.uid', $this->getMemberId())
            ->join('yz_order_goods', function ($join) use($goods_id) {
            $join->on('yz_order.id', 'yz_order_goods.order_id')
                ->where('yz_order_goods.goods_id', $goods_id);
        })->first();

        return $pass;
    }

    public function getShare()
    {
        $activity_id = (int)request()->lotteryId;
        $member_id = $this->getMemberId();

        $activityModel = DrawActivityModel::uniacid()->find($activity_id);

        $shareOfToday = DrawShareModel::uniacid()
            ->whereBetween('created_at', $this->getToday())
            ->where('activity_id', $activity_id)
            ->where('member_id', $member_id)
            ->first();
        $shareModel = DrawShareModel::where('activity_id', $activity_id)
            ->where('member_id', $member_id)
            ->first();

        if ($activityModel->partake_times == 0 && $shareOfToday) {
            return $this->errorJson('今日您已分享过了！');
        }elseif($activityModel->partake_times == 1 && $shareModel) {
            return $this->errorJson('您已分享过了！');
        }elseif ($activityModel->partake_times == 0 && empty($shareOfToday)) {
            $shareModel = new DrawShareModel();
            $shareModel->member_id = $member_id;
            $shareModel->uniacid = \YunShop::app()->uniacid;
            $shareModel->activity_id = $activity_id;
            $shareModel->times = $activityModel->days_share_times;
            if ($shareModel->save()) {
                return $this->successJson('分享成功！');
            }
        }elseif ($activityModel->partake_times == 1 && empty($shareModel)) {
            $shareModel = new DrawShareModel();
            $shareModel->member_id = $member_id;
            $shareModel->uniacid = \YunShop::app()->uniacid;
            $shareModel->activity_id = $activity_id;
            $shareModel->times = $activityModel->somebody_share_times;
            if ($shareModel->save()) {
                return $this->successJson('分享成功！');
            }
        }
    }

    public function getToday()
    {
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        return [$beginToday, $endToday];
    }

    public function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }

    public function getUiniacid()
    {
        return \YunShop::app()->uniacid;
    }
}