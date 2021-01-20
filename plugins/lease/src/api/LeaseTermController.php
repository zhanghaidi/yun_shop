<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/7 
 */

namespace Yunshop\LeaseToy\api;

use app\common\components\ApiController;
use Yunshop\LeaseToy\models\LeaseTermModel;
use Yunshop\LeaseToy\services\LeaseToyRightsService;
/**
* 租期列表
*/
class LeaseTermController extends ApiController
{

    public function index()
    {

        $set = \Setting::get('plugin.lease_toy');
        //租期数据
        $list = LeaseTermModel::apiList()->get();

        //可用等级权益
        $level = LeaseToyRightsService::getMemberRights(\Yunshop::app()->getMemberId());

        $data = [
            'list' => $list,
            'level' => $level,
            'lease_toy_set' => [
                'pact_title' => $set['pact_title'],
                'lease_toy_pact' => html_entity_decode($set['lease_toy_pact']),
            ],
        ];
        $this->successJson('ok', $data);
    }

}