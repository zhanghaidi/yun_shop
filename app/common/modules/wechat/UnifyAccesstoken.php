<?php
/**
 * Created by PhpStorm.
 * Desc: 统一获取accesstoken， fixby-zlt
 * User: zlt
 * Date: 2020/09/29
 * Time: 15:20
 */
namespace app\common\modules\wechat;
use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use app\common\models\AccountWechats;

class UnifyAccesstoken
{
    /**
     * 缓存前缀
     */
    protected static $prefix = 'unifyaccesstoken.access_token.';

    /**
     *  API
     */
    const API_TOKEN_GET = 'https://www.aijuyi.net/api/accesstoken.php?secret=secret';

    /**
     * 统一获取accesstoken
     * @param string                       $appid 小程序或者公众号appid
     * @param int                          $type  1 公众号 4 小程序
     * @param string                       $forceRefresh 是否刷新缓存
     * @return string
     */

    public static function getAccessToken($appid = '', $forceRefresh = false){
        if(empty($appid))
            return false;
        $cache_key = self::getCacheKey($appid);
        $cache_val = Cache::get($cache_key);

        if (!$cache_val || $cache_val['expire_at'] <= time() || $forceRefresh) {
            $account_wachats = \app\common\models\AccountWechats::where('key',$appid)->first();
            if(!empty($account_wachats->uniacid)){
                $type = 1;
            }else{
                $type = 4;
            }
            $url = self::API_TOKEN_GET . "&type={$type}&appid={$appid}";
            $response = self::curl_get($url);
            $result = json_decode($response,true);
            if (!is_array($result) || !array_key_exists('accesstoken', $result)) {
                Log::error('UnifyAccesstoken获取access_token失败:', [
                    'appid' => $appid,
                    'url' => $url,
                    'result' => $result,
                ]);
                return false;
            }
            $cache_val = ['token' => $result['accesstoken'], 'expire_at' => time() + 200];
            Cache::put($cache_key, $cache_val, 3);
        }
        return $cache_val['token'];
    }

    /**
     * 统一获取accesstoken
     * @param string                       $appid 小程序或者公众号appid
     * @return string
     */

    public static function getCacheKey($appid){
        return self::$prefix . $appid;
    }

    /**
     * @param string $url get请求地址
     * @param int $httpCode 返回状态码
     * @return mixed
     */
    protected static function curl_get($url,&$httpCode = 0){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        //不做证书校验，部署在linux环境下请改位true
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }

    //获取微擎统生产环境统一AccessToken
    public static function unifiedProductionAccessToken($appid)
    {
        $account_wachats = AccountWechats::where('key', $appid)->first();
        if(!empty($account_wachats->uniacid)){
            $type = 1;
        }else{
            $type = 4;
        }
        $url = "https://www.aijuyi.net/api/accesstoken.php?type={$type}&appid={$appid}&secret=secret";
        $res = ihttp_get($url);
        $content = @json_decode($res['content'],true);
        if(!$content['accesstoken']){
            throw new AppException('调用生产统一access_tokenAPI出错');
        }
        return $content['accesstoken'];

    }
}