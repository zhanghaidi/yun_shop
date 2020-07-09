<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/21
 * Time: 11:36 AM
 */

namespace Yunshop\Supplier\frontend\order\models;

use Yunshop\Supplier\common\models\Supplier;

class PreOrder extends \app\frontend\modules\order\models\PreOrder
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->plugin_id = Supplier::PLUGIN_ID;
    }

}