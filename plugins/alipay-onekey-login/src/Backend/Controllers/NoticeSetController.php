<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 上午10:17
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;
use Yunshop\Love\Common\Services\SetService;

class NoticeSetController extends BaseController
{
    /**
     * 查看设置
     */
    public function see()
    {
        $temp_list = MessageTemp::getList();

        return view('Yunshop\Love::Backend.noticeSet', [
            'set' => SetService::getLoveSet(),
            'temp_list' => $temp_list
        ])->render();
    }

    /**
     * 保存设置
     * @return mixed|string
     */
    public function store()
    {
        $requestData = \YunShop::request()->love;
        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message("设置保存成功",Url::absoluteWeb('plugin.love.Backend.Controllers.notice-set.see'));
            }
            $this->error($result);
        }
        return $this->see();
    }

}
