<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/4/27
 * Time: 16:45
 */

namespace Yunshop\GroupPurchase\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class IpWhiteList extends BaseModel
{
    public $table = 'yz_ip_whitelist';

    public function getIpWhiteListByIp ($ip_address) {
        return self::where('ip_address',$ip_address)->value('ip_address');
    }

}