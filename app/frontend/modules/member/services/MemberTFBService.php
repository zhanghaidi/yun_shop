<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2020/3/12
 * Time: 下午8:08
 */

namespace app\frontend\modules\member\services;


use app\common\exceptions\ShopException;
use app\common\services\Session;
use app\frontend\modules\member\models\MemberModel;
use Yunshop\Haifen\common\service\HfSign;

class MemberTFBService extends MemberService
{
    private $appGateWay;
    private $appId;
    private $appSecret;

    public function login()
    {
        $this->verify(request()->input());
    }

    /**
     * 验证登录状态
     *
     * @return bool
     */
    public function checkLogged()
    {
        return $this->verify(request()->input());
    }

    public function verify($data)
    {
        $this->getAppData();

        $hfSign = new HfSign();
        $hfSign->setKey($this->appSecret);

        if ($hfSign->verify($data)) {
            $MemberModel = MemberModel::getId($data['i'], $data['mob_parent']);

            if (!is_null($MemberModel)) {
                Session::set('member_id', $MemberModel->uid);

                return true;
            }
        }

        return false;
    }

    private function getAppData()
    {
        $appData = \Setting::get('plugin.haifen_set');

        if (is_null($appData) || 0 == $appData['status']) {
            throw new ShopException('应用未启用');
        }

        if (empty($appData['app_id']) || empty($appData['app_secret'])) {
            throw new ShopException('应用参数错误');
        }

        if ($appData['app_id'] != request()->input('appid')) {
            throw new ShopException('访问身份异常');
        }

        $this->appGateWay = $appData['app_gateway'];
        $this->appId = $appData['app_id'];
        $this->appSecret = $appData['app_secret'];
    }
}