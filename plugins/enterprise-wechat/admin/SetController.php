<?php

namespace Yunshop\EnterpriseWechat\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;

/**
* 
*/
class SetController extends BaseController
{

	public function index()
	{
		$request = request();
		$set = Setting::get('plugin.enterprise-wechat');
		$alipaySet = \Yunshop::request()->setdata;
		if ($alipaySet) {
			if (!isset($alipaySet['app']) && !empty($set['app'])) {
				$set['bind_mobile'] = $alipaySet['bind_mobile'];
				if (Setting::set('plugin.enterprise-wechat', $set)) {
                	return $this->message('设置成功', Url::absoluteWeb('plugin.enterprise-wechat.admin.set.index'));
            	}
			} else {
				$alipaySet = $this->verification($alipaySet);
				if (Setting::set('plugin.enterprise-wechat', $alipaySet)) {
					//开启强制绑定手机
					$this->openBindMobile();
                	return $this->message('设置成功', Url::absoluteWeb('plugin.enterprise-wechat.admin.set.index'));
            	}

			}
            return $this->error('设置失败');
		}

		return view('Yunshop\EnterpriseWechat::admin.set', [
			'setdata' => $set,
		])->render();
	}


	private function verification($alipaySet)
	{
		$this->validate([
            'setdata.app.alipay_appid' => 'required',
            'setdata.app.private_key'	=> 'required',
            'setdata.app.alipay_public_key' => 'required',
        ],null,[
        	'setdata.app.alipay_appid.required' => '支付宝APPID不能为空',
        	'setdata.app.private_key.required' => '应用私钥不能为空',
        	'setdata.app.alipay_public_key.required' => '支付宝公钥不能为空',
        ]);
		
		$alipaySet['app']['alipay_appid'] = trim($alipaySet['app']['alipay_appid']);
		$alipaySet['app']['private_key'] = base64_encode(trim($alipaySet['app']['private_key']));
		$alipaySet['app']['alipay_public_key'] = base64_encode(trim($alipaySet['app']['alipay_public_key']));

		return $alipaySet;
	}

	private function openBindMobile()
	{
		$member_set = Setting::get('shop.member');
		if (is_null($member_set)) {
			return Setting::set('shop.member', ['is_bind_mobile' => '1']);
		}

        if (!$member_set['is_bind_mobile']) {
        	$member_set['is_bind_mobile'] = '1';
        	return Setting::set('shop.member', $member_set);
        }
	}


	public function delAlipaySet()
	{
		$set = Setting::get('plugin.alipay_onekey_login');

		$set['app'] = [];

		if (Setting::set('plugin.alipay_onekey_login', $set)) {

                return $this->message('清空成功', Url::absoluteWeb('plugin.alipay-onekey-login.admin.set.index'));
            } else {
                $this->error('清空失败');
            }
	}
}