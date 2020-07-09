<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/27
 * Time: 下午5:31
 */

namespace Yunshop\Printer\common\models;


use app\common\models\BaseModel;

class PrintLog extends BaseModel
{
    public $table = 'yz_printed_log';
    public $timestamps = true;
    protected $guarded = [''];
    protected $casts = [
        'content' => 'json'
    ];

}