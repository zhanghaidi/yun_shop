<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/5
 */

namespace Yunshop\LeaseToy\api;

use app\common\components\ApiController;

/**
* 租赁插件设置
*/
class LeaseToyController extends ApiController
{

    protected $set;

    public function __construct() 
    {
        $this->set = \Setting::get('plugin.lease_toy');

        parent::__construct();
    }

    /**
     * 插件是否开启
     */
    public function whetherEnabled()
    {
        if (app('plugins')->isEnabled('lease-toy')) {
            if ($this->set['is_lease_toy']) {
                return $this->successJson('ok', $this->set['is_lease_toy']);
            }
        } 
        return $this->errorJson('no', $this->set['is_lease_toy']);

    }
}