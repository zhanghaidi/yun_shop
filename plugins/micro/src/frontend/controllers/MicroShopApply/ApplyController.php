<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/17
 * Time: 上午9:37
 */

namespace Yunshop\Micro\frontend\controllers\MicroShopApply;

use app\common\components\ApiController;
use Setting;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopLevel;

class ApplyController extends ApiController
{
    public function index()
    {
        $member_id = \YunShop::app()->getMemberId();
        $micro_shop = MicroShop::getMicroShopByMemberId($member_id);
        $set = Setting::get('plugin.micro');
        $levels = MicroShopLevel::getLevelList()->get();
        $levels->map( function ($level) use ($micro_shop){
            $level->not_show = false;
            $level->hasOneGoods->thumb = yz_tomedia($level->hasOneGoods->thumb);
            if ($micro_shop) {
                $level->not_show = $micro_shop->hasOneMicroShopLevel->level_weight >= $level->level_weight ? true : false;
            }
        });
        $levels = $levels->filter(function($level){
            return $level->hasOneGoods != null;
        });
        if (isset($levels)) {
            return $this->successJson('成功', [
                'status'            => 1,
                'create_order_api'  => 'order.goodsBuy',
                'micro_thumb'       => yz_tomedia($set['micro_thumb']),
                'signature'         => html_entity_decode($set['signature']),
                'levels'            => $levels
            ]);
        } else {
            return $this->errorJson('未检测到数据!', [

            ]);
        }
    }
}