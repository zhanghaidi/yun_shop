<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-01-10
 * Time: 14:16
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

namespace app\frontend\modules\goods\controllers;


use app\common\components\ApiController;
use app\common\exceptions\ShopException;
use app\common\exceptions\UniAccountNotFoundException;
use app\common\helpers\Client;
use app\common\models\Member;
use app\common\models\UniAccount;
use app\common\modules\shop\models\Shop;
use app\frontend\modules\member\services\factory\MemberFactory;
use Yunshop\Designer\models\ViewSet;

class GoodsApiController extends ApiController
{
    public function preAction()
    {
        $is_new_goods = 0;
        if (app('plugins')->isEnabled('designer')) {
            //商品模版
            $view_set = ViewSet::uniacid()->where('type', 'goods')->first();
            if (!empty($view_set) && $view_set->names == '02') {
                $is_new_goods = 1;
            }
        }

        if (!UniAccount::checkIsExistsAccount(\YunShop::app()->uniacid)) {
            throw new UniAccountNotFoundException('无此公众号', ['login_status' => -2]);
        }

        if (empty($is_new_goods)) {
            parent::preAction();
            $relaton_set = Shop::current()->memberRelation;

            $mid = Member::getMid();
            $mark = \YunShop::request()->mark;
            $mark_id = \YunShop::request()->mark_id;

            $type = \YunShop::request()->type;

            if (Client::setWechatByMobileLogin(\YunShop::request()->type)) {
                $type = 5;
            }

            if (self::is_alipay()) {
                $type = 8;
            }

            $member = MemberFactory::create($type);

            if (!$member->checkLogged()) {
                if (($relaton_set->status == 1 && !in_array($this->action, $this->ignoreAction))
                    || ($relaton_set->status == 0 && !in_array($this->action, $this->publicAction))
                ) {
                    $this->jumpUrl($type, $mid);
                }
            } else {
                if (\app\frontend\models\Member::current()->yzMember->is_black) {
                    throw new ShopException('黑名单用户，请联系管理员', ['login_status' => -1]);
                }

                //发展下线
                Member::chkAgent(\YunShop::app()->getMemberId(), $mid, $mark ,$mark_id);
            }
        }
    }
}