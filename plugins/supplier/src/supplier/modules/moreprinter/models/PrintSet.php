<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/5
 * Time: 1:39 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\models;


use Yunshop\MorePrinter\common\models\PrintSet as ParentSet;

class PrintSet extends ParentSet
{
    public function hasOneSupplierSet()
    {
        return $this->hasOne(SupplierSet::class, 'ms_id', 'id');
    }

    public function scopeBuild($query, $w_uid)
    {
        return $query->whereHas('hasOneSupplierSet', function ($item) use ($w_uid) {
            $item->where('user_uid', $w_uid);
        });
    }

    public function store($model)
    {
        $user_uid = $model->user_uid;
        unset($model->user_uid);
        $model->save();
        SupplierSet::create([
            'ms_id' => $model->id,
            'user_uid' => $user_uid
        ]);
    }

    public function scopeByPluginId($query, $plugin_id = 92)
    {
        return $query->where('plugin_id', $plugin_id);
    }
}