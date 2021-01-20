<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/8
 * Time: 上午11:04
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;
use Yunshop\Mryt\common\models\MemberParent;
use Yunshop\Mryt\services\CommonService;
use Yunshop\Mryt\services\UpgrateAwardService;

class SetController extends BaseController
{
    public function test()
    {
        if (!request()->uid) {
            return 'UID必填';
        }
        (new UpgrateAwardService(request()->uid, \YunShop::app()->uniacid))->handleAward();
        dd('ok');
        exit;
    }

    public function index()
    {
        $set = CommonService::getSet();

        $temp_list = MessageTemp::getList();

        if (\Request::getMethod() == 'POST') {
            $data = \YunShop::request()->set;
            //$yz_notice = \YunShop::request()->yz_notice;

            if($data){
                if (\Setting::set('plugin.mryt_set', $data)) {
                    return $this->message('设置成功', Url::absoluteWeb('plugin.mryt.admin.set.index'));
                } else {
                    return $this->error('设置失败');
                }
            }
        }


        return view('Yunshop\Mryt::admin.set', [
            'set' => $set,
            'temp_list' => $temp_list
        ])->render();
    }
}