<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/5 下午4:14
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Backend\Controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;
use Yunshop\Sign\Common\Services\SetService;

class NoticeSetController extends BaseController
{
    protected $success_url = 'plugin.sign.Backend.Controllers.notice-set.see';


    protected $view_value = 'Yunshop\Sign::Backend.notice_set';


    public function see()
    {
        return view($this->view_value,[
            'sign' => SetService::getSignSet(),
            'temp_list' => MessageTemp::select('id', 'title')->get()
        ])->render();
    }



    public function store()
    {
        $requestData = \YunShop::request()->sign;

        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message("设置保存成功",Url::absoluteWeb($this->success_url));
            }
            $this->error($result);
        }
        return $this->see();
    }

}
