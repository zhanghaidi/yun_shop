<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/12/4
 * Time: 5:42 PM
 */

namespace Yunshop\Supplier\supplier\modules\moreprinter\models;


use app\common\models\BaseModel;

class SupplierTemp extends BaseModel
{
    public $table = 'yz_supplier_printer_temp';
    public $timestamps = true;
    protected $guarded = [''];
}