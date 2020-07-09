<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/11/7
 * Time: 2:04 PM
 */

namespace Yunshop\Love\Common\Services;


use app\common\facades\Setting;
use app\common\models\UniAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Yunshop\Love\Common\Jobs\LoveRecycleJob;
use Yunshop\Love\Common\Models\LoveTradingModel;

class TimedTaskRecycleService
{
    /**
     * @var array
     */
    private $love_set;


    public function handle()
    {
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable() ?: [];
        foreach ($uniAccount as $u) {
            Setting::$uniqueAccountId = \YunShop::app()->uniacid = $u->uniacid;
            $this->love_set = Setting::get('plugin.love_trading');
            $this->recycle();
        }
    }


    private function recycle()
    {
        $is_recycle = $this->isRecycle();
        if ($is_recycle) {
            $this->_recycle();
            Log::info("公司回购>>公众号" . Setting::$uniqueAccountId . "已分配公司回购队列");
        }
        Log::info("公司回购>>公众号" . Setting::$uniqueAccountId . "未设置公司回购");
    }

    public function _recycle()
    {
        //todo 需要做数据运算，当数据量过大时，需要分段获取运行
        $recycleModels = $this->getRecycleModels();

        //没有需要回购数据直接完成
        if ($recycleModels->isEmpty()) {
            return true;
        }

        //不需要数据事物，单会员执行回购时增加事物，本次回收失败，下次回购会继续回购（只要保证单会员回购数据完成就可以）
        foreach ($recycleModels as $key => $recycleModel) {

            $recycleModel->status = -1;
            $recycleModel->save();

            dispatch(new LoveRecycleJob($recycleModel));
        }
        return true;
    }

    /**
     * Do you set up a repurchase
     *
     * @return bool
     */
    private function isRecycle()
    {
        if (!$this->love_set['trading']) {
            return false;
        }
        if (!$this->love_set['recycl'] || $this->love_set['recycl'] <= 0) {
            return false;
        }
        return true;
    }

    /**
     * 获取所有需要回购的爱心值出售
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRecycleModels()
    {
        $minute = $this->love_set['recycl'] * 60;
        $recycle_time = Carbon::now()->subMinutes($minute)->timestamp;

        return LoveTradingModel::uniacid()->where('status', 0)->where('created_at', '<=', $recycle_time)->get();
    }
}
