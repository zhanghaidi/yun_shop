<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/4
 * Time: 5:36 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\temp;


use Yunshop\MorePrinter\admin\temp\ListController as ParentList;
use Yunshop\Supplier\supplier\modules\moreprinter\models\Temp;

class ListController extends ParentList
{
    protected function common()
    {
        return [
            'add_url' => 'plugin.supplier.supplier.modules.moreprinter.temp.operation.add',
            'edit_url' => 'plugin.supplier.supplier.modules.moreprinter.temp.operation.edit'
        ];
    }

    protected function getModel()
    {
        return new Temp();
    }
}