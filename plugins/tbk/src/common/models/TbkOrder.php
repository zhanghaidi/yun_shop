<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/10
 * Time: 10:51
 */

namespace Yunshop\Tbk\common\models;


class TbkOrder extends \Yunshop\Tbk\common\models\BaseModel
{
    public $table = 'yz_tbk_order';
    public $timestamps = true;
    protected $guarded = [''];

}