<?php

namespace Yunshop\Wechat\admin\setting;

use app\common\components\BaseController;
use \Illuminate\Support\Facades\URL;
use app\platform\modules\application\models\UniacidApp;


/**
* 
*/
class SettingController extends BaseController
{
    public function index($request)
    {

        $data = \Setting::get('plugin.wechat');
        //https://dev8.yunzshop.com/api/wechat?id=4
        $address = request()->getSchemeAndHttpHost() .'/api/wechat?id='.\Yunshop::app()->uniacid;

        if(empty($data)){
            //token 3-32位数字或字母   aeskey 43位a-zA-Z0-9
            $token   = $this->generateRandomString(32);

            $aes_key = $this->generateRandomString(43);

            \Setting::set('plugin.wechat.token', $token);

            \Setting::set('plugin.wechat.aes_key', $aes_key);

            \Setting::set('plugin.wechat.address', $address);

            $data['token']   = $token;

            $data['aes_key'] = $aes_key;

            $data['address'] = $address;
        }

        $set_data = $request['form_data'];

        if ($set_data){
            $uniacid_app = new UniacidApp();  
            $uniacid = $uniacid_app->where('id',\Yunshop::app()->uniacid )->get();
            if ($uniacid->count() <= 0){
                $result = $uniacid_app->where('key',$set_data['app_id'])->get();
                if ($result->count() > 0){
                    return $this->errorJson('AppID已存在，请重新输入');
                }
            }else{
                $result = $uniacid_app->whereNotIn('id',[\Yunshop::app()->uniacid])->where('key',$set_data['app_id'])->get();
                if ($result->count() > 0){
                    return $this->errorJson('AppID已存在，请重新输入');
                }
            }


            $uniacid_app->where('id',\Yunshop::app()->uniacid )->update([
                'key'     => $set_data['app_id'],

                'secret'  => $set_data['app_secret'],

                'token'   => $set_data['token'],

                'encodingaeskey'  => $set_data['aes_key']
                ]);

            $set_data['address'] = $address;

            if (\Setting::set('plugin.wechat', $set_data)) {

                return $this->successJson('ok');

            }

            return $this->errorJson('error');

        }

        return view('Yunshop\Wechat::admin.setting.setting', [

            'data'       => json_encode($data),

            ])->render();
    }

    public function newToken($request)
    {
        $set_token = request('token');

        if (\Setting::set('plugin.wechat.token', $set_token)) {

            return $this->successJson('ok');

        }

        return $this->errorJson('error');

    }

    public function newKey($request)
    {
        $set_aeskey = request('aes_key');

        if (\Setting::set('plugin.wechat.aes_key', $set_aeskey)) {

            return $this->successJson('ok');

        }

        return $this->errorJson('error');

    }

    public function generateRandomString($length = 0) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, strlen($characters) - 1)];

        }

        return $randomString;

    }





}