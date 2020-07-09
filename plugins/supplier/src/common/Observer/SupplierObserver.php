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

class SupplierObserver extends BaseObserver
{
    public function __construct()
    {
        //echo 2;exit;
    }

    public function saving(Model $model)
    {
        //echo '<pre>';print_r(1);exit;
    }

    public function saved(Model $model)
    {
        //echo '<pre>';print_r(2);exit;
        //$this->pluginObserver('observer.supplier', $model, 'saved', 1);
    }

    public function created(Model $model)
    {
        //echo '<pre>';print_r(3);exit;
        //$this->pluginObserver('observer.supplier.withdraw', $model, 'created', 1);
    }

    public function updating(Model $model)
    {
        $this->pluginObserver('observer.supplier', $model, 'updating');
    }

    public function updated(Model $model)
    {

    }

    public function deleted(Model $model)
    {
        //echo '<pre>';print_r(6);exit;
    }
}