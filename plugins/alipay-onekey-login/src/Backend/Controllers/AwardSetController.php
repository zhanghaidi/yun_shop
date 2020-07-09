<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 上午9:38
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Love\Common\Services\SetService;

class AwardSetController extends BaseController
{
    /**
     * 查看设置
     */
    public function see()
    {

        for ($i = 0; $i <= 23; $i++) {
            $hourData[$i] = [
                'key' => $i,
                'name' => $i . ":00",
            ];
        }
        $commission_set = \Setting::get('plugin.commission');
        $pluginCommission = \YunShop::plugin()->get('commission');
        if ($pluginCommission) {
            $commission_level = AgentLevel::getLevels()->get();
        }
        $love_commission_set = SetService::getLoveSet();
        $love_commission_set['commission'] = unserialize($love_commission_set['commission']);

        return view('Yunshop\Love::Backend.awardSet',[
            'love' => SetService::getLoveSet(),
            'hourData' => $hourData,
            'pluginCommission' => $pluginCommission,
            'set' => $commission_set,
            'levels' => $commission_level,
            'love_commission_set' => $love_commission_set['commission'],
        ])->render();
    }

    /**
     * 保存设置
     * @return mixed|string
     */
    public function store()
    {
        $requestData = \YunShop::request()->love;
        $requestData['commission'] = serialize($requestData['commission']);

        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message("设置保存成功",Url::absoluteWeb('plugin.love.Backend.Controllers.award-set.see'));
            }
            $this->error($result);
        }
        return $this->see();
    }

}
