<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 上午10:18
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Carbon\Carbon;
use Yunshop\Love\Backend\Modules\Love\Services\LoveActivationService;
use Yunshop\Love\Common\Jobs\LoveActivation;
use Yunshop\Love\Common\Models\MemberLove;
use Yunshop\Love\Common\Services\SetService;

class ActivationSetController extends BaseController
{
    /**
     * 查看设置
     */
    public function see()
    {
        return view('Yunshop\Love::Backend.activationSet', [
            'love'           => SetService::getLoveSet(),
            'activationTime' => LoveActivationService::activationTime(),
            'week_data'      => $this->getWeekData(),
            'day_data'       => $this->getDayData()
        ])->render();
    }

    /**
     * 保存设置
     * @return mixed|string
     */
    public function store()
    {
        $requestData = \YunShop::request()->love;

        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message("设置保存成功", Url::absoluteWeb('plugin.love.Backend.Controllers.activation-set.see'));
            }
            $this->error($result);
        }
        return $this->see();
    }

    public function activation()
    {
        dispatch(new LoveActivation(\YunShop::app()->uniacid));

        return $this->message("添加激活队列成功");
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
                $i + 1 => "当天" . $i . ":00 激活",
            ];
        }
        return $dayData;
    }

}
