<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/19
 * Time: 上午11:42
 */

namespace Yunshop\VideoDemand\api;


use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\VideoDemand\models\SlideModel;

class VideoDemandController extends ApiController
{
    public $set;
    public $uid;

    public function __construct()
    {
        parent::__construct();

        $this->set = Setting::get('plugin.video_demand');
        $this->uid = \YunShop::app()->getMemberId();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 幻灯片
     */
    public function getVideoSlide()
    {
        // plugin.video-demand.api.video-demand.get-video-slide
        $data = SlideModel::getSlide()->select('id', 'slide_name', 'link', 'thumb', 'status')->where('status', 1)->get();

        foreach ($data as &$slide) {
            $slide->thumb = replace_yunshop(yz_tomedia($slide->thumb));
        }

        if ($data) {
            return $this->successJson('成功', $data);
        }
        return $this->errorJson('未检测到数据!', $data);
    }


}