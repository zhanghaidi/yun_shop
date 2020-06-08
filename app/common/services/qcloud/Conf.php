<?php
namespace app\common\services\qcloud;

use app\platform\modules\system\models\SystemSetting;


class Conf {

    // Cos php sdk version number.
    const VERSION = 'v4.2.2';
    const API_COSAPI_END_POINT = 'http://region.file.myqcloud.com/files/v2/';

    public static $appid;
    public static $secretid;
    public static $tsecretkey;
    /**
     * Please refer to http://console.qcloud.com/cos to fetch your app_id, secret_id and secret_key.
     *
     * @return array
     */
    public static function config()
    {
        if (config('app.framework') == 'platform') {
            $remote = SystemSetting::settingLoad('remote', 'system_remote');
        } else {
            global $_W;
            $remote = $_W['setting']['remote'];
        }

        return [
            'APP_ID'     => self::$appid?:$remote['cos']['appid'],
            'SECRET_ID'  => self::$secretid?:$remote['cos']['secretid'],
            'SECRET_KEY' => self::$tsecretkey?:$remote['cos']['secretkey']
        ];
    }

    /**
     * Get the User-Agent string to send to COS server.
     */
    public static function getUserAgent() {
        return 'cos-php-sdk-' . self::VERSION;
    }
}
