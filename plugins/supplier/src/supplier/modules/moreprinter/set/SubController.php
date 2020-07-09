<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/5
 * Time: 1:42 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\set;


use Yunshop\MorePrinter\admin\set\SubController as ParentSub;
use Yunshop\Supplier\supplier\modules\moreprinter\models\PrintSet;

class SubController extends ParentSub
{
    protected function common()
    {
        $set_model = PrintSet::getSet(\YunShop::app()->uid)->first();
        if (!$set_model) {
            $set_model = new PrintSet();
        }
        return [
            'set_model' => $set_model
        ];
    }
}