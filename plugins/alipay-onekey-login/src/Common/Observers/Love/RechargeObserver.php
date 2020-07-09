<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/25
 * Time: 2:31 PM
 */

namespace Yunshop\Love\Common\Observers\Love;


use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;
use Yunshop\Love\Common\Services\Love\RechargeService;

class RechargeObserver extends BaseObserver
{
    public function created(Model $model)
    {
        (new RechargeService($model))->tryRecharge();
    }
}
