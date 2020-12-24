<?php

namespace Yunshop\XiaoeClock\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;

/**
 * 打卡任务管理控制器
 */
class ClockController extends BaseController
{

    public function index()
    {
        $set = Setting::get('plugin.circle');
        $requestModel = \YunShop::request()->circle;
        if ($requestModel) {
            if (Setting::set('plugin.circle', $requestModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.circle.admin.set'));
            } else {
                $this->error('设置失败');
            }
        }


        return view('Yunshop\Circle::admin.set',
            [
                'set' => $set,
            ]
        )->render();
    }

//增加打卡活动
    public function addClock()
    {

    }

//增加打卡活动任务
    public function addClockTask()
    {

    }

//编辑打卡活动
    public function editClock()

    {

    }

//编辑打卡活动
    public function editClockTask()
    {

    }
}