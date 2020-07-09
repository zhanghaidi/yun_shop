<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/26 下午2:20
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Love\Common\Services\SetService;

class BaseSetController extends BaseController
{
    /**
     * 查看设置
     * @return string
     * @throws \Throwable
     */
    public function see()
    {
        return view('Yunshop\Love::Backend.baseSet',[
            'love' => SetService::getLoveSet(),
            'teamDividend' => app('plugins')->isEnabled('team-dividend'),
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
                return $this->message("设置保存成功",Url::absoluteWeb('plugin.love.Backend.Controllers.base-set.see'));
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
