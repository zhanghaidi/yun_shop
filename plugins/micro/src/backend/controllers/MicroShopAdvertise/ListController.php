<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/16
 * Time: 上午11:29
 */

namespace Yunshop\Micro\backend\controllers\MicroShopAdvertise;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Micro\common\models\MicroShopCarousel;

class ListController extends BaseController
{
    public function index()
    {
        $slide = MicroShopCarousel::getSlides()->paginate(10);
        $pager = PaginationHelper::show($slide->total(), $slide->currentPage(), $slide->perPage());

        return view('Yunshop\Micro::backend.MicroShopAdvertise.list', [
            'slide'  => $slide?$slide->toArray()['data']:[],
            'pager'  => $pager
        ])->render();
    }

    public function add()
    {
        $slideModel = new MicroShopCarousel();
        $requestSlide = \YunShop::request()->slide;
        if($requestSlide) {
            $requestSlide['is_carousel'] = 0;
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
                    return $this->message('创建成功', Url::absoluteWeb('plugin.micro.backend.controllers.MicroShopAdvertise.list'));
                }else{
                    $this->error('创建失败');
                }
            }
        }
        return view('Yunshop\Micro::backend.MicroShopAdvertise.info', [
            'slideModel' => $slideModel
        ])->render();
    }

    public function edit()
    {
        $id = \YunShop::request()->id;
        $slideModel = MicroShopCarousel::getSlideByid($id);
        if(!$slideModel){
            return $this->message('无此记录或已被删除','','error');
        }

        $requestSlide = \YunShop::request()->slide;
        if($requestSlide) {
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
                    return $this->message('保存成功', Url::absoluteWeb('plugin.micro.backend.controllers.MicroShopAdvertise.list'));
                }else{
                    $this->error('保存失败');
                }
            }
        }

        return view('Yunshop\Micro::backend.MicroShopAdvertise.info', [
            'slideModel' => $slideModel
        ])->render();
    }

    public function delete()
    {
        $id = \YunShop::request()->id;
        $slide = MicroShopCarousel::getSlideByid($id);
        if(!$slide) {
            return $this->message('无此记录或已经删除','','error');
        }

        $result = MicroShopCarousel::deletedSlide($id);
        if($result) {
            return $this->message('删除成功',Url::absoluteWeb('plugin.micro.backend.controllers.MicroShopAdvertise.list'));
        }else{
            return $this->message('删除失败','','error');
        }
    }
}