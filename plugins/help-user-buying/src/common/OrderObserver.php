<?php

namespace Yunshop\HelpUserBuying\common;

use app\backend\modules\goods\models\Discount;
use app\backend\modules\goods\models\Share;
use app\backend\modules\goods\services\DiscountService;
use app\backend\modules\goods\services\Privilege;
use app\backend\modules\goods\services\PrivilegeService;
use app\common\traits\MessageTrait;
use Illuminate\Database\Eloquent\Model;


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:24
 */
class OrderObserver extends \app\common\observers\BaseObserver
{
    use MessageTrait;


    public function saving(Model $model)
    {
        // dump($model);
    }

    public function saved(Model $model)
    {
        // dump($model);
    }

    public function created(Model $model)
    {
        // dump($model);
    }

    public function updating(Model $model)
    {

    }

    public function updated(Model $model)
    {
    }

    public function deleted(Model $model)
    {
    }


}