<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/29
 * Time: 14:17
 */

namespace Yunshop\Supplier\supplier\controllers;


use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\supplier\models\Slide;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class SlideController extends SupplierCommonController
{
    public function index()
    {
        $supplier_uid = \YunShop::app()->uid;
        $pageSize = 20;
        $slide = Slide::getSlides()->whereHas('hasOneSupplier', function ($query) use ($supplier_uid) {
            $query->where('uid', $supplier_uid);
        })->paginate($pageSize);
        $pager = PaginationHelper::show($slide->total(), $slide->currentPage(), $slide->perPage());
        return view('Yunshop\Supplier::supplier.slide.slide-list', [
            'slide' => $slide->items(),
            'pager' => $pager,
        ])->render();
    }

    public function create()
    {
        $slideModel = new Slide();

        $requestSlide = \YunShop::request()->slide;

        if($requestSlide) {
            //将数据赋值到model
            $slideModel->setRawAttributes($requestSlide);
            //其他字段赋值
            $slideModel->uniacid = \YunShop::app()->uniacid;
            $slideModel->supplier_uid = \YunShop::app()->uid; //供应商辅助id

            //字段检测
            $validator = $slideModel->validator($slideModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($slideModel->save()) {
                    //显示信息并跳转
                    return $this->message('创建成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.slide.index'));
                }else{
                    $this->error('创建失败');
                }
            }
        }

        return view('Yunshop\Supplier::supplier.slide.slide-info', [
            'slideModel' => $slideModel
        ])->render();
    }

    public function edit()
    {
        $id = \YunShop::request()->id;
        $slideModel = Slide::getSlideByid($id);
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
                    return $this->message('保存成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.slide.index'));
                }else{
                    $this->error('保存失败');
                }
            }
        }

        return view('Yunshop\Supplier::supplier.slide.slide-info', [
            'slideModel' => $slideModel
        ])->render();
    }

    public function deleted()
    {
        $id = \YunShop::request()->id;
        $slide = Slide::getSlideByid($id);
        if(!$slide) {
            return $this->message('无此记录或已经删除','','error');
        }

        $result = Slide::deletedSlide($id);
        if($result) {
            return $this->message('删除成功',Url::absoluteWeb('plugin.supplier.supplier.controllers.slide.index'));
        }else{
            return $this->message('删除失败','','error');
        }
    }
}