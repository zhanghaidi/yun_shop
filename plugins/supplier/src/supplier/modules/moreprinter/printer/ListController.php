<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/4
 * Time: 2:48 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\printer;


use Yunshop\MorePrinter\admin\printer\ListController as ParentList;
use Yunshop\Supplier\supplier\modules\moreprinter\models\Printer;

class ListController extends ParentList
{
    // plugin.supplier.supplier.modules.moreprinter.printer.list.index

    protected function common()
    {
        return [
            'add_url' => 'plugin.supplier.supplier.modules.moreprinter.printer.operation.add',
            'edit_url' => 'plugin.supplier.supplier.modules.moreprinter.printer.operation.edit',
        ];
    }

    protected function getModel()
    {
        return new Printer();
    }
}