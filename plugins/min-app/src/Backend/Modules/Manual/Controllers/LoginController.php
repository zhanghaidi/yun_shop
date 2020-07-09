<?php
/**
 * WeChat - Applet by BaoJia Li
 *
 * @author      BaoJia Li
 * @User        king/QQ:995265288
 * @Tool        PhpStorm
 * @Date        2019/12/18  9:21 AM
 * @link        https://gitee.com/li-bao-jia
 */

namespace Yunshop\MinApp\Backend\Modules\Manual\Controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use Ixudra\Curl\Facades\Curl;

class LoginController extends BaseController
{
    /**
     * @var string
     */
    private $url = 'http://134.175.117.38/api/devtools/login';

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $description;


    public function index()
    {
        $result = Curl::to($this->url)->withData($this->params())->post();

        $result && $result = json_decode($result, true);

        if (isset($result['status']) && $result['status'] == 'SUCCESS') {
            return $this->successJson('SUCCESS', [
                'qr_code'    => $result['data']['qr_code'],
                'identifier' => $result['data']['identifier']
            ]);
        }
        if (isset($result['status']) && $result['status'] == 'WAIT') {
            return $this->successJson('SUCCESS', [
                'count' => $result['data']['count'],
                'time'  => $result['data']['time']
            ]);
        }
        return $this->errorJson(isset($result['message']) ? $result['message'] : 'FAIL');
    }

    private function params()
    {
        $this->version();
        $this->description();

        $keySecret = $this->keySecret();

        return [
            'type'         => request()->type ?: 0,
            'app_id'       => $this->appId(),
            'domain'       => request()->getHttpHost(),
            'account'      => \YunShop::app()->uniacid,
            'version'      => $this->version,
            'cloud_key'    => $keySecret['key'],
            'description'  => $this->description,
            'cloud_secret' => $keySecret['secret']
        ];
    }

    private function keySecret()
    {
        return Setting::get('shop.key');
    }

    private function version()
    {
        !isset($this->version) && $this->version = request()->version;

        return $this->version;
    }

    private function description()
    {
        !isset($this->description) && $this->description = request()->description;

        return $this->description;
    }

    private function appId()
    {
        return $this->appletSet()['key'];
    }

    private function appletSet()
    {
        return Setting::get('plugin.min_app');
    }
}
