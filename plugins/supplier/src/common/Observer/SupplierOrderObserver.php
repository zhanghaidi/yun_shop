<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 下午5:38
 */

namespace Yunshop\Supplier\common\Observer;


use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class SupplierOrderObserver extends BaseObserver
{
    public function saved(Model $model)
    {
        $this->pluginObserver('observer.supplier', $model, 'created');
    }

    public function updated(Model $model)
    {
        $this->pluginObserver('observer.supplier', $model, 'updated');
    }
}