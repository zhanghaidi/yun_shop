<?php

namespace Yunshop\LeaseToy\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;

/**
* 
*/
class LeaseToySetController extends BaseController
{
    
    public function index()
    {
        $set = Setting::get('plugin.lease_toy');

        $leaseToy = \Yunshop::request()->setdata;

        if ($leaseToy) {
            if (Setting::set('plugin.lease_toy', $leaseToy)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.lease-toy.admin.lease-toy-set.index'));
            } else {
                $this->error('设置失败');
            }
        }

        $temp_list = MessageTemp::select('id', 'title')->get();

        return view('Yunshop\LeaseToy::admin.set', 
            [
                'setdata' => $set,
                'temp_list' => $temp_list,
            ])->render();

    }
}
