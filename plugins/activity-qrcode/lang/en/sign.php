<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/5 下午1:49
 * Email: livsyitian@163.com
 */


$sign = \Yunshop\Sign\Common\Services\SetService::getSignSet('sign_name') ?:'签到';

return [
    'name' => $sign,

];

