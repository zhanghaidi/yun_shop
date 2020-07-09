<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31 0031
 * Time: 下午 5:49
 */

namespace Yunshop\HelpCenter\services;

use app\common\traits\ValidatorTrait;

class SetService
{
    use ValidatorTrait;
    //保存设置
    public function storeSet($array)
    {
        $validator = (new SetService())->validator($array);
        if ($validator->fails()) {
            return $validator->messages();
        }
        foreach ($array as $key => $item) {
            \Setting::set('help-center.'.$key, $item);
        }
        return true;
    }
}