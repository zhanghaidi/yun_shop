<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/4
 * Time: 3:50 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\models;


class Printer extends \Yunshop\MorePrinter\common\models\Printer
{
    public function hasOneSupplierPrinter()
    {
        return $this->hasOne(SupplierPrinter::class, 'mp_id', 'id');
    }

    public function scopeBuild($query, $w_uid)
    {
        return $query->whereHas('hasOneSupplierPrinter', function ($item) use ($w_uid) {
            $item->where('user_uid', $w_uid);
        });
    }

    public function scopeByPluginId($query, $plugin_id = 92)
    {
        return $query->where('plugin_id', $plugin_id);
    }

    public function store($model)
    {
        $user_uid = $model->user_uid;
        unset($model->user_uid);
        $model->save();
        SupplierPrinter::create([
            'mp_id' => $model->id,
            'user_uid' => $user_uid
        ]);
    }
}