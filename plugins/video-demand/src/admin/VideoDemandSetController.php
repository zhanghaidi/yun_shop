<?php

namespace Yunshop\VideoDemand\admin;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/09
 * Time: 下午2:01
 */
class VideoDemandSetController extends BaseController
{
    public function index()
    {
        $set = Setting::get('plugin.video_demand');
        $videoDemandModel = \YunShop::request()->setdata;
        $slideData = \YunShop::request()->slideData;

        if ($videoDemandModel) {
            if (Setting::set('plugin.video_demand', $videoDemandModel)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.video-demand.admin.video-demand-set.index'));
            } else {
                return $this->error('设置失败');
            }
        }

        $temp_list = MessageTemp::select('id', 'title')->get();
        return view('Yunshop\VideoDemand::admin.set', [
            'set' => $set,
            'temp_list' => $temp_list,
        ])->render();
    }

}