<?php

namespace Yunshop\VideoDemand\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\VideoDemand\models\SlideModel;
use Yunshop\VideoDemand\services\SlideService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/09
 * Time: 下午2:01
 */
class VideoSlideController extends BaseController
{
    public function index()
    {
        $slide = (new SlideService)->getSlideData();
        return view('Yunshop\VideoDemand::admin.slide-list', [
            'slide' => $slide,
        ])->render();
    }

    /**
     * @return mixed|string
     */
    public function add()
    {
        $slideModel = new SlideModel();

        $requestSlide = \YunShop::request()->slide;

        if ($requestSlide) {
            //将数据赋值到model
            $slideModel->setRawAttributes($requestSlide);
            //其他字段赋值
            $slideModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = $slideModel->validator($slideModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($slideModel->save()) {
                    //显示信息并跳转
                    return $this->message('创建成功', Url::absoluteWeb('plugin.video-demand.admin.video-slide.index'));
                } else {
                    $this->error('创建失败');
                }
            }
        }

        return view('Yunshop\VideoDemand::admin.slide-info', [
            'slideModel' => $slideModel,
        ])->render();
    }

    /**
     * @return mixed|string
     */
    public function edit()
    {
        $id = \YunShop::request()->id;
        $slideModel = SlideModel::getSlideByid($id);
        if (!$slideModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }

        $requestSlide = \YunShop::request()->slide;
        if ($requestSlide) {
            //将数据赋值到model
            $slideModel->setRawAttributes($requestSlide);
            //字段检测
            $validator = $slideModel->validator($slideModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($slideModel->save()) {
                    //显示信息并跳转
                    return $this->message('保存成功', Url::absoluteWeb('plugin.video-demand.admin.video-slide.index'));
                } else {
                    $this->error('保存失败');
                }
            }
        }
        return view('Yunshop\VideoDemand::admin.slide-info', [
            'slideModel' => $slideModel,
        ])->render();
    }

    /**
     * @return mixed
     */
    public function deleted()
    {
        $id = \YunShop::request()->id;
        $slide = SlideModel::getSlideByid($id);
        if (!$slide) {
            return $this->message('无此记录或已经删除', '', 'error');
        }

        $result = SlideModel::deletedSlide($id);
        if ($result) {
            return $this->message('删除成功', Url::absoluteWeb('plugin.video-demand.admin.video-slide.index'));
        } else {
            return $this->message('删除失败', '', 'error');
        }
    }


}