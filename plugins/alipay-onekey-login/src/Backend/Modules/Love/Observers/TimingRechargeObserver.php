<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/26
 * Time: 3:56 PM
 */

namespace Yunshop\Love\Backend\Modules\Love\Observers;


use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;
use Yunshop\Love\Backend\Modules\Love\Services\TimingRechargeService;

class TimingRechargeObserver extends BaseObserver
{
    public function creating(Model $model)
    {
        return (new TimingRechargeService())->addTimingQueue($model);
    }

}
