<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 15:23
 */

namespace Yunshop\Community\api\frontend;


use app\common\components\BaseController;
use app\common\services\finance\PointService;
use app\backend\modules\member\models\Member;
use app\common\models\finance\PointLog;
use app\common\events\MessageEvent;

class PointLogController extends BaseController
{
    public function addPoint()
    {
        $request = request();
        $member = Member::getMemberById($request->uid);
        \Log::debug('----member---', $member);
        if ($request->num > 0) {
            $pointData = array(
                'uniacid' => \YunShop::app()->uniacid,
                'point_income_type' => 1,
                'member_id' => $request->uid,
                'point_mode' => '22',
                'point' => $request->num,
                'remark' => '圈子社区奖励: 用户 ' . $request->uid .'获得积分奖励' . $request->num .'个',
                'after_point' => $member->credit1
            );
            $point_model = PointLog::create($pointData);
            \Log::debug('----point_model---', $point_model);
            if (!isset($point_model)) {
                return false;
            }
            self::messageNotice($member, $request);
        }
    }

    public function messageNotice($member, $request)
    {
        $template_id = \Setting::get('shop.notice')['point_change'];
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '昵称', 'value' => $member->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
            ['name' => '积分变动金额', 'value' => $request->num],
            ['name' => '积分变动类型', 'value' => (new PointService())->getModeAttribute(22)],
            ['name' => '变动后积分数值', 'value' => $member->credit1]
        ];
        event(new MessageEvent($member->uid, $template_id, $params, $url=''));
    }
}