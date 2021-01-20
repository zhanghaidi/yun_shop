<?php

namespace Yunshop\LeaseToy\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\LeaseToy\models\ReturnAddressModel;
use app\common\models\Address;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2017/2/28
* Time: 上午10:04
*/
class ReturnAddressController extends BaseController
{
    protected $pageSize = 15;

    public function index()
    {
        $search = \Yunshop::request()->get('search');


        $list = ReturnAddressModel::getAddressList($search)->get();

        $list = $this->getMap($list);
        // dd($list);
        return view('Yunshop\LeaseToy::admin.return-address-list', [
            'list' => $list,
        ])->render();
    }


    public function add()
    {
        $returnAddress = new ReturnAddressModel();

        $requestData = \Yunshop::request()->address;

        if ($requestData) {
            //将数据赋值到model
            $returnAddress->setRawAttributes($requestData);
            //其他字段赋值
            $returnAddress->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = $returnAddress->validator($returnAddress->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //默认地址
                if ($requestData['is_default'] == 1) {
                    ReturnAddressModel::reviseDefault();
                }

                //数据保存
                if ($returnAddress->save()) {
                    //显示信息并跳转
                    return $this->message('创建成功', Url::absoluteWeb('plugin.lease-toy.admin.return-address.index'));
                } else {
                    $this->error('创建失败');
                }
            }
        }

        return view('Yunshop\LeaseToy::admin.return-address-form', [
            'returnAddress' => $returnAddress,
        ])->render();

    }

    public function edit()
    {
        $id = \Yunshop::request()->id;
        $returnAddress = ReturnAddressModel::getReturnAddressid($id);
        if (!$returnAddress) {
            return $this->message('无记录或已被删除', '', 'error');
        }

        $requestData = \Yunshop::request()->address;
        if ($requestData) {
            //将数据赋值到model
            $returnAddress->setRawAttributes($requestData);


            //字段检测
            $validator = $returnAddress->validator($returnAddress->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //默认地址
                if ($requestData['is_default'] == 1) {
                    ReturnAddressModel::reviseDefault();
                }

                //数据保存
                if ($returnAddress->save()) {
                    //显示信息并跳转
                    return $this->message('保存成功', Url::absoluteWeb('plugin.lease-toy.admin.return-address.index'));
                } else {
                    $this->error('保存失败');
                }
            }
        }

        return view('Yunshop\LeaseToy::admin.return-address-form', [
            'returnAddress' => $returnAddress,
            'id' => \Yunshop::request()->id,
        ])->render();

    }


    /**
     * @return mixed
     */
    public function deleted()
    {
        $id = \Yunshop::request()->id;
        $returnAddress = ReturnAddressModel::getReturnAddressid($id);
        if (!$returnAddress) {
            return $this->message('无记录或已被删除', '', 'error');
        }

        $result = ReturnAddressModel::deletedReturnAddressid($id);
        if ($result) {
            return $this->message('删除成功', Url::absoluteWeb('plugin.lease-toy.admin.return-address.index'));
        } else {
            return $this->message('删除失败', '', 'error');
        }
    }

    //地址处理
    private function getMap($list)
    {
        if (!$list) {
            return $list;
        }
        $list->map(function ($row){
            $province = Address::select()->where('id', $row->province_id)->first();
            $city = Address::select()->where('id', $row->city_id)->first();
            $district = Address::select()->where('id', $row->district_id)->first();
            $address = $province['areaname'] . $city['areaname'] . $district['areaname'];
            $row->province = $address;
        });
        return $list;
    }
}