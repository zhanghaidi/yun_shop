<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2018/3/6
 * Time: 15:18
 */

namespace Yunshop\LeaseToy\models\order;

use Yunshop\LeaseToy\models\LeaseOrderModel;

class Goods extends \app\frontend\models\Goods
{
    public function scopePluginId($query, $pluginId = 0)
    {
        return parent::scopePluginId($query, LeaseOrderModel::PLUGIN_ID); // TODO: Change the autogenerated stub
    }

}