<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/20
 * Time: 下午2:27
 */

namespace Yunshop\Exhelper\common\models;

use app\common\models\BaseModel;
use app\framework\Database\Eloquent\Builder;

class PrintStatus extends BaseModel
{
    public $table = 'yz_exhelper_print';
    protected $guarded = [''];
    public $timestamps = false;

}