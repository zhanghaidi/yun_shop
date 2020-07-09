<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/5
 * Time: 10:36 AM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\models;


use app\common\models\BaseModel;

class SupplierSet extends BaseModel
{
    public $table = 'yz_supplier_printer_set';
    public $timestamps = true;
    protected $guarded = [''];
}