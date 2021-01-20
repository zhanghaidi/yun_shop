<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/17
 * Time: 下午7:20
 */

namespace Yunshop\Micro\frontend\controllers\MicroShop;


use app\common\components\ApiController;
use app\common\requests\Request;
use Yunshop\Micro\common\models\MicroShop;
use Setting;

class SetShopController extends ApiController
{
    public function index()
    {
        $set = Setting::get('plugin.micro');
        $micro_shop = MicroShop::getMicroShopByMemberId(\YunShop::app()->getMemberId());

        if (isset($micro_shop)) {
            return $this->successJson('成功', [
                'status'        => 1,
                'micro_shop'    => $micro_shop,
                'api'           => request()->getSchemeAndHttpHost() . '/addons/yun_shop/api.php?i='.\YunShop::app()->uniacid.'&route=plugin.micro.frontend.controllers.MicroShop.set-shop.upload',
                'select_goods_thumb'=> replace_yunshop(yz_tomedia($set['select_goods_thumb'])),
            ]);
        } else {
            return $this->errorJson('未检测到数据', [
                'status'        => 0
            ]);
        }
    }

    public function edit()
    {
        $shop_set_data = \YunShop::request()->shop_set;
        if ($shop_set_data) {
            $shop_set_data = json_decode($shop_set_data, true);
            $result = MicroShop::where('member_id', \YunShop::app()->getMemberId())->update($shop_set_data);
            if ($result) {
                return $this->successJson('保存成功', [
                    'status'    => 1
                ]);
            } else {
                return $this->errorJson('保存失败', [
                    'status'    => -1
                ]);
            }
        }
        return $this->errorJson('数据为空', [
            'status'    => -1
        ]);
    }

    public function editSignature()
    {
        $signature = request()->signature;
        if ($signature) {
            $result = MicroShop::where('member_id', \YunShop::app()->getMemberId())->update(['signature' => $signature]);
            if ($result) {
                return $this->successJson('保存成功', [
                    'status'    => 1
                ]);
            } else {
                return $this->errorJson('保存失败', [
                    'status'    => -1
                ]);
            }
        }
        return $this->errorJson('数据为空', [
            'status'    => -1
        ]);
    }

    public function editBackground()
    {
        $shop_background = request()->background;
        if ($shop_background) {
            $result = MicroShop::where('member_id', \YunShop::app()->getMemberId())->update(['shop_background' => $shop_background]);
            if ($result) {
                return $this->successJson('保存成功', [
                    'status'    => 1
                ]);
            } else {
                return $this->errorJson('保存失败', [
                    'status'    => -1
                ]);
            }
        }
        return $this->errorJson('数据为空', [
            'status'    => -1
        ]);
    }

    public function upload(Request $request)
    {
        $path = $request->file('file')->store('avatars');
        return $this->successJson('保存成功', [
            'img'    => request()->getSchemeAndHttpHost().'/addons/yun_shop/storage/app/'.$path
        ]);
    }
}