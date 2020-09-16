<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/8 上午10:44
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Common\Services;


use app\common\traits\ValidatorTrait;

class SetService
{
    use ValidatorTrait;


    public static function getSignSet($key = '')
    {
        $uniacid = \YunShop::app()->uniacid;
        if (!\Cache::has('plugin.sign.set_' . $uniacid)) {
            $sign_set = array_pluck(\Setting::getAllByGroup('sign')->toArray(), 'value', 'key');
            $sign_set['cumulative'] = \Setting::get('sign.cumulative');
            \Cache::put('plugin.sign.set_' . $uniacid, $sign_set, 3600);
        } else {
            $sign_set = \Cache::get('plugin.sign.set_' . $uniacid);
        }
        if ($key) {
            return  isset($sign_set[$key]) ? $sign_set[$key] : '';
        }
        if (!app('plugins')->isEnabled('love')) {
            unset($sign_set['award_love_min']);
            unset($sign_set['award_love_max']);
        }else{
            $sign_set['love_status'] = 1;
        }

        return $sign_set;
    }



    public static function storeSet($array)
    {
        $validator = (new SetService())->validator($array);
        if ($validator->fails()) {
            return $validator->messages()->first();
        }
        foreach ($array as $key => $item) {
            \Setting::set('sign.' . $key, $item);
        }
        \Cache::forget('plugin.sign.set_' . \YunShop::app()->uniacid);
        return true;
    }


    public function rules()
    {
        return [

        ];
    }

    public function atributeNames()
    {
        return [];
    }



}
