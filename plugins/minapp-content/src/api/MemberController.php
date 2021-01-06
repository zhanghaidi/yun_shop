<?php

namespace Yunshop\MinappContent\api;
use app\common\components\ApiController;
use app\frontend\modules\member\services\factory\MemberFactory;

class MemberController extends ApiController
{
    //获取用户关注公众号状态
    public function getFollow()
    {
        $uniacid = \YunShop::app()->uniacid;
        $user_id = \YunShop::app()->getMemberId();

        $service_user = pdo_get('diagnostic_service_user', array('ajy_uid' => $user_id));
        //粉丝是否关注养居益公众号
        $fan_user = pdo_get('mc_mapping_fans', array('uniacid' => $uniacid, 'unionid' => $service_user['unionid']));

        $is_follow = $fan_user['follow'] ? $fan_user['follow'] : 0 ;

        return $this->successJson('success', array('is_follow'=> $is_follow));

    }

}
