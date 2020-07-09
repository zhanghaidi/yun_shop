<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-07-16
 * Time: 11:01
 */

namespace Yunshop\Love\Backend\Controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Love\Common\Services\SetService;

class RechargeSetController extends BaseController
{
    /**
     * 查看设置
     */
    public function see()
    {
        return view('Yunshop\Love::Backend.rechargeSet',[
            'love' => SetService::getLoveSet(),
            'commissionLevels' => $this->getCommissionLevel()
        ])->render();
    }

    /**
     * 保存设置
     * @return mixed|string
     * @throws \Throwable
     */
    public function store()
    {
        $requestData = \YunShop::request()->love;
        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message("设置保存成功",Url::absoluteWeb('plugin.love.Backend.Controllers.recharge-set.see'));
            }
            $this->error($result);
        }
        return $this->see();
    }

    private function getCommissionLevel()
    {
        $commissionLevels = [];
        if (app('plugins')->isEnabled('commission')) {
            $commissionLevels = AgentLevel::select('id', 'name')->uniacid()->get();
        }
        return $commissionLevels;
    }


}
