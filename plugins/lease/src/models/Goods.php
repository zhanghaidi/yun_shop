<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/7
 * Time: 14:57
 */
namespace Yunshop\LeaseToy\models;

use Yunshop\LeaseToy\models\LeaseOrderModel;

class Goods extends \app\common\models\Goods
{
    
    public function scopePluginId($query,$pluginId = 0)
    {
        return $query->where('plugin_id', LeaseOrderModel::PLUGIN_ID);
    }
}