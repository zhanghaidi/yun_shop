<?php

namespace Yunshop\LeaseToy\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\LeaseToy\models\LeaseTermModel;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2017/2/28
* Time: 上午10:04
*/
class LeaseTermController extends BaseController
{
    //protected $pageSize = 15;

    public function index()
    {
        $search = \Yunshop::request()->get('search');

        $list = LeaseTermModel::getList();

        // dd($list);
        return view('Yunshop\LeaseToy::admin.lease-term-list', [
            'list' => $list,
        ])->render();
    }


    public function add()
    {
        $leaseTerm = new LeaseTermModel();

        $requestData = \Yunshop::request()->term;

        if ($requestData) {
            //将数据赋值到model
            $leaseTerm->setRawAttributes($requestData);
            //其他字段赋值
            $leaseTerm->uniacid = \YunShop::app()->uniacid;
            //字段检测
            $validator = $leaseTerm->validator($leaseTerm->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {

                //数据保存
                if ($leaseTerm->save()) {
                    //显示信息并跳转
                    return $this->message('创建成功', Url::absoluteWeb('plugin.lease-toy.admin.lease-term.index'));
                } else {
                    $this->error('创建失败');
                }
            }
        }

        return view('Yunshop\LeaseToy::admin.lease-term-form', [
            'leaseTerm' => $leaseTerm,
        ])->render();

    }

    public function edit()
    {
        $id = \Yunshop::request()->id;
        $leaseTerm = LeaseTermModel::find($id);
        if (!$leaseTerm) {
            return $this->message('无记录或已被删除', '', 'error');
        }

        $requestData = \Yunshop::request()->term;
        if ($requestData) {
            //将数据赋值到model
            $leaseTerm->setRawAttributes($requestData);
        
            //字段检测
            $validator = $leaseTerm->validator($leaseTerm->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {

                //数据保存
                if ($leaseTerm->save()) {
                    //显示信息并跳转
                    return $this->message('保存成功', Url::absoluteWeb('plugin.lease-toy.admin.lease-term.index'));
                } else {
                    $this->error('保存失败');
                }
            }
        }

        return view('Yunshop\LeaseToy::admin.lease-term-form', [
            'leaseTerm' => $leaseTerm,
            'leaseTerm_id' => \Yunshop::request()->id,
        ])->render();

    }


    /**
     * @return mixed
     */
    public function deleted()
    {
        $id = \Yunshop::request()->id;
        $leaseTerm = LeaseTermModel::find($id);
        if (!$leaseTerm) {
            return $this->message('无记录或已被删除', '', 'error');
        }

        $result = LeaseTermModel::deletedTerm($id);
        if ($result) {
            return $this->message('删除成功', Url::absoluteWeb('plugin.lease-toy.admin.lease-term.index'));
        } else {
            return $this->message('删除失败', '', 'error');
        }
    }
}