<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-28
 * Time: 12:45
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Appletslive\common\services;


class BaseService
{
    protected $appId;
    protected $secret;

    public function getRooms($token)
    {
        $url = 'http://api.weixin.qq.com/wxa/business/getliveinfo?access_token=' . $token;

        $post_data = [
            'start' => 0,
            'limit' => 100,
        ];

        $result = self::curlPost($url, json_encode($post_data), []);

        return json_decode($result, true);
    }

    public function getReplays($token, $rid)
    {
        $url = 'http://api.weixin.qq.com/wxa/business/getliveinfo?access_token=' . $token;

        $post_data = [
            'action' => 'get_replay',
            'room_id' => $rid,
            'start' => 0,
            'limit' => 100,
        ];

        $result = self::curlPost($url, json_encode($post_data), []);

        return json_decode($result, true);
    }

    public function getToken($appId, $secret)
    {
        $result = self::curlPost($this->requestUrl($appId, $secret), '', []);

        return json_decode($result, true);
    }

    private static function curlPost($url, $post_data, $options = []){
        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if(!empty($options)){
            curl_setopt_array($ch, $options);
        }

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    protected function requestUrl($appId, $secret)
    {
        return 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='. $appId .'&secret=' . $secret;
    }
}