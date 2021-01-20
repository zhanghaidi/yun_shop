<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/23
 * Time: 2:56 PM
 */

namespace Yunshop\Nominate\frontend;


use app\common\components\ApiController;
use Carbon\Carbon;

class CommonController extends ApiController
{
    public $today_time_start;
    public $yesterday_time_start;
    public $this_week_time_start;
    public $this_month_time_start;

    public function setTime()
    {
        $this->today_time_start = strtotime(Carbon::today());
        $this->yesterday_time_start = strtotime(Carbon::yesterday());
        $this->this_week_time_start = strtotime(date('Y-m-d', strtotime("this week Monday", time())));
        $this->this_month_time_start = mktime(0,0,0,date('m'),1,date('Y'));
    }
}