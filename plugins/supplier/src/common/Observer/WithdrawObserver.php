<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 上午9:28
 */

namespace Yunshop\Supplier\common\Observer;

use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class WithdrawObserver extends BaseObserver
{
    public function saving(Model $model)
    {

    }

    public function saved(Model $model)
    {

    }

    public function created(Model $model)
    {
        $this->pluginObserver('observer.supplier', $model, 'insert');
    }

    public function updating(Model $model)
    {

    }

    public function updated(Model $model)
    {
        $this->pluginObserver('observer.withdraw', $model, 'updated', 1);
    }

    public function deleted(Model $model)
    {

    }
}