<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/4
 * Time: 5:56 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\temp;


use Yunshop\MorePrinter\admin\temp\OperationController as ParentOperation;
use Yunshop\Supplier\supplier\modules\moreprinter\models\Temp;

class OperationController extends ParentOperation
{
    protected function common()
    {
        $model = Temp::getTempById(intval(request()->id), \YunShop::app()->uid)->first();
        return [
            'return_url' => 'plugin.supplier.supplier.modules.moreprinter.temp.list.index',
            'model' => $model,
            'new_model' => new Temp(),
            'plugin_id' => \app\common\modules\shop\ShopConfig::current()->get('plugins.printer.supplier')
        ];
    }
}