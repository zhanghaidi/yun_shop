<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/4
 * Time: 4:16 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\models;


use app\common\models\BaseModel;

class SupplierPrinter extends BaseModel
{
    public $table = 'yz_supplier_printer';
    public $timestamps = true;
    protected $guarded = [''];
}