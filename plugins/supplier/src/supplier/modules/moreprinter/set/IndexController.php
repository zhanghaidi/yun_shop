<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/4
 * Time: 2:48 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\set;


use Yunshop\MorePrinter\admin\set\IndexController as ParentIndex;
use Yunshop\Supplier\supplier\modules\moreprinter\models\Printer;
use Yunshop\Supplier\supplier\modules\moreprinter\models\PrintSet;
use Yunshop\Supplier\supplier\modules\moreprinter\models\Temp;

class IndexController extends ParentIndex
{
    protected function common()
    {
        return [
            'plugin_id' => \app\common\modules\shop\ShopConfig::current()->get('plugins.printer.supplier'),
            'printers' => Printer::getList(\YunShop::app()->uid)->get(),
            'temps' => Temp::getList(\YunShop::app()->uid)->get(),
            'set' => PrintSet::getSet(\YunShop::app()->uid)->first(),
            'sub_url' => yzWebUrl('plugin.supplier.supplier.modules.moreprinter.set.sub.index')
        ];
    }
}