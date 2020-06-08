<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/10
 * Time: 下午2:00
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\services\PointService;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\MemberGroup;
use app\common\services\finance\PointToLoveService;
use Carbon\Carbon;
use app\common\helpers\Url;

class PointSetController extends BaseController
{
//    public function test1()
//    {
//        (new PointToLoveService())->transferStart();
//
//        dd('手动转入成功');
//    }
//    public function test2()
//    {
//        Setting::set('point.transfer_love', [
//            'last_month' => date('m') -1,
//            'last_week'  => date('W') -1,
//            'last_day'   => date('d') -1
//        ]);
//
//        dd('重置转入时间成功');
//    }
    /**
     * @name 积分基础设置
     * @return array $set
     * @author yangyang
     */
    public function index()
    {
        $point_data = PointService::getPointData(
            \YunShop::request()->set,
            \YunShop::request()->enough,
            \YunShop::request()->give
        );
        if ($point_data) {
            $point_data = $this->verifySetData($point_data);
            $result = (new PointService())->verifyPointData($point_data);
            if ($result) {
                (new \app\common\services\operation\PointSetLog(['old' => $this->pointSet(), 'new' => $point_data], 'update'));
                return $this->message($result, Url::absoluteWeb('finance.point-set'));
            }
        }

        return view('finance.point.set', $this->resultData());
    }

    private function resultData()
    {
        return [
            'set'          => $this->pointSet(),
            'day_data'     => $this->getDayData(),
            'week_data'    => $this->getWeekData(),
            'memberLevels' => $this->memberLevels(),
            'memberGroups' => $this->memberGroups()
        ];
    }

    /**
     * 转换类型
     *
     * @param array $point_data
     * @return mixed array
     * @author yangyang
     */
    private function verifySetData($point_data)
    {
        $point_data['money'] = floatval($point_data['money']);
        $point_data['money_max'] = floatval($point_data['money_max']);
        $point_data['give_point'] = trim($point_data['give_point']);
        $point_data['enough_money'] = floatval($point_data['enough_money']);
        $point_data['enough_point'] = floatval($point_data['enough_point']);
        return $point_data;
    }

    //爱心值插件名称
    private function loveName()
    {
        $loveName = Setting::get('love.name');

        return $loveName ? $loveName : '爱心值';
    }

    //会员等级列表
    private function memberLevels()
    {
        return MemberLevel::getMemberLevelList();
    }

    //会员分组列表
    private function memberGroups()
    {
        return MemberGroup::records()->get();
    }

    private function pointSet()
    {
        $set =  Setting::get('point.set');

        $set['love_name'] = $this->loveName();

        return $set;
    }

    private function getWeekData()
    {
        return [
            Carbon::SUNDAY    => '星期日',
            Carbon::MONDAY    => '星期一',
            Carbon::TUESDAY   => '星期二',
            Carbon::WEDNESDAY => '星期三',
            Carbon::THURSDAY  => '星期四',
            Carbon::FRIDAY    => '星期五',
            Carbon::SATURDAY  => '星期六',
        ];
    }

    /**
     * 返回一天24时，对应key +1, 例：1 => 0:00
     * @return array
     */
    private function getDayData()
    {
        $dayData = [];
        for ($i = 0; $i <= 23; $i++) {
            $dayData += [
                $i + 1 => "当天" . $i . ":00 转入",
            ];
        }
        return $dayData;
    }
}
