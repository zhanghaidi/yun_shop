<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/11/27
 * Time: 10:15 AM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\services;


use Yunshop\MorePrinter\common\services\SourceParent;
use Yunshop\Supplier\common\models\SupplierGoods;
use Yunshop\Supplier\supplier\modules\moreprinter\models\Printer;
use Yunshop\Supplier\supplier\modules\moreprinter\models\PrintSet;
use Yunshop\Supplier\supplier\modules\moreprinter\models\Temp;

class Source implements SourceParent
{
    private $order;
    private $user_uid = 0;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function enable()
    {
        foreach ($this->order->hasManyOrderGoods as $order_goods) {
            $supplier_goods = SupplierGoods::with([
                'hasOneSupplier'
            ])->where('goods_id', $order_goods->goods_id)->first();
            if ($supplier_goods) {
                $this->user_uid = $supplier_goods->hasOneSupplier->uid;
                return true;
            }
        }
        return false;
    }

    public function getPrintSet()
    {
        return PrintSet::getSet($this->user_uid)->first();
    }

    public function getPrintersBySetting($printSet)
    {
        return Printer::select()
            ->whereHas('hasOneSupplierPrinter', function ($item) {
                $item->where('user_uid', $this->user_uid);
            })
            ->whereIn('id', $printSet->printer_ids)
            ->where('status', 1)
            ->get();
    }

    public function getTempBySetting($printSet)
    {
        return Temp::select()
            ->whereHas('hasOneSupplierTemp', function ($item) {
                $item->where('user_uid', $this->user_uid);
            })
            ->where('id', $printSet->temp_id)
            ->first();
    }
}