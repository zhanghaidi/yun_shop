<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/4
 * Time: 4:52 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\printer;


use Yunshop\MorePrinter\admin\printer\OperationController as ParentOperation;
use Yunshop\Supplier\supplier\modules\moreprinter\models\Printer;

class OperationController extends ParentOperation
{
    // plugin.supplier.supplier.modules.moreprinter.printer.operation
    protected function common()
    {
        $model = Printer::getPrinterById(intval(request()->id), \YunShop::app()->uid)->first();
        return [
            'plugin_id' => \app\common\modules\shop\ShopConfig::current()->get('plugins.printer.supplier'),
            'new_model' => new Printer(),
            'model' => $model,
            'return_url' => 'plugin.supplier.supplier.modules.moreprinter.printer.list.index',
        ];
    }
}