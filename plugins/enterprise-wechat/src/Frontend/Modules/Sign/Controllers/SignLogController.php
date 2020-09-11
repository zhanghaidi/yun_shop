<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/8 上午11:42
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Frontend\Modules\Sign\Controllers;


use app\common\components\ApiController;
use Carbon\Carbon;
use Yunshop\Sign\Frontend\Models\Sign;
use Yunshop\Sign\Frontend\Models\SignLog;

class SignLogController extends ApiController
{
    /**
     * @var Sign
     */
    private $signModel;

    /**
     * @var
     */
    private $signLogModel;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $month;

    public function __construct()
    {
        parent::__construct();
        $this->signModel = $this->getSignModel();
        $this->signLogModel = $this->getSignLogModel();

        $this->page = $this->getPostPage();
        $this->year = $this->getPostYear();
        $this->month = $this->getPostMonth();
    }

    //签到页面接口
    public function index()
    {
        $data = [
            'sign_name'     => trans('Yunshop\Sign::sign.plugin_name'),
            'sign_status'   => $this->signModel->sign_status,
            'sign_total'    => $this->signLogModel->count() . "天",
            'continue_days' => $this->signModel->cumulative_name,
            'cumulative'    => $this->signModel->cumulative_award,
            'sign_log'      => $this->getCalendarData()
        ];
        return $this->successJson('ok', $data);
    }

    //签到记录接口
    public function log()
    {
        $data = [
            'sign_name'     => trans('Yunshop\Sign::sign.plugin_name'),
            'continue_days' => $this->signModel->cumulative_name,
            'sign_total'    => $this->signLogModel->count() . "天",
            'cumulative'    => $this->signModel->cumulative_award,
            'sign_log'      => $this->getSignLogData() ? $this->getSignLogData()->toArray()['data'] : []
        ];

        return $this->successJson('ok', $data);
    }

    /**
     * 签到首页 日历数据重构
     *
     * @return array
     */
    private function getCalendarData()
    {
        $sign_log = $this->getSignLogData();
        !$sign_log && $sign_log == [];

        $result = [];
        foreach ($sign_log as $key => $item) {
            $result[] = (int)date('d', $item->created_at->timestamp) - 1;
        }

        return $result;
    }

    /**
     * 获取签到 model
     *
     * @return Sign
     */
    private function getSignModel()
    {
        return Sign::first() ?: new Sign();
    }

    /**
     * 获取签到记录 log
     *
     * @return mixed
     */
    private function getSignLogModel()
    {
        return SignLog::select('status', 'created_at', 'award_point', 'award_coupon', 'award_love');
    }

    /**
     * @return mixed
     */
    private function getSignLogData()
    {
        list($startTime, $endTime) = $this->searchTime();

        return $this->signLogModel->whereBetween('created_at', [$startTime, $endTime])
            ->orderBy('created_at', 'desc')
            ->paginate(32, '', '', $this->getPostPage());
    }

    private function searchTime()
    {
        $startTime = Carbon::create($this->year, $this->month)->startOfMonth()->timestamp;
        $endTime = Carbon::create($this->year, $this->month)->endOfMonth()->timestamp;

        return [$startTime, $endTime];
    }

    /**
     * 前段提交分页值
     *
     * @return int
     */
    private function getPostPage()
    {
        return \YunShop::request()->page ?: 1;
    }

    /**
     * 前段提交月份值
     *
     * @return int
     */
    private function getPostMonth()
    {
        return (int)\YunShop::request()->month ?: (int)date("m");
    }

    /**
     * 前段提交年份值
     *
     * @return int
     */
    private function getPostYear()
    {
        return (int)\YunShop::request()->year ?: (int)date("Y");
    }

}
