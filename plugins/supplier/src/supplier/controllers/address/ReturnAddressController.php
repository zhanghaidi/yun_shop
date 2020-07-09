<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/4/25
 * Time: 9:31
 */

namespace Yunshop\Supplier\supplier\controllers\address;

use Yunshop\Supplier\common\controllers\SupplierCommonController;
use app\common\models\member\Address;
use app\common\services\Session;
use app\backend\modules\goods\models\ReturnAddress;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Street;
use Setting;

class ReturnAddressController extends SupplierCommonController
{
    const PLUGINS_ID = 1;//0商城，31门店，1经销商
    public $supplier_id;

    public function preAction()
    {
        parent::preAction();
        $this->supplier_id = Session::get('supplier')['id'];
    }

    /**
     * 退货地址列表
     * @return array $item
     */
    public function index()
    {

        $pageSize = 10;
        $list = ReturnAddress::uniacid()
            ->where('plugins_id', self::PLUGINS_ID)
            ->where('supplier_id',$this->supplier_id)
            ->orderBy('id', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($pageSize)
            ->toArray();
//        dd($list);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('Yunshop\Supplier::supplier.return.list', [
            'list' => $list,
            'pager' => $pager,
        ])->render();
    }

    /**
     * 退货地址添加
     * @return array $item
     */
    public function add()
    {
        $addressModel = new ReturnAddress();
        $requestAddress = \YunShop::request()->address;
        if ($requestAddress) {
            if (!$requestAddress['province_id']) {
                return $this->message('请选择省份', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.add'));
            }
            if (!$requestAddress['city_id']) {
                return $this->message('请选择城市', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.add'));
            }
            if (!$requestAddress['district_id']) {
                return $this->message('请选择区域', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.add'));
            }
            if (!$requestAddress['street_id']) {
                return $this->message('请选择街道', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.add'));
            }
            //将数据赋值到model
            $addressModel->setRawAttributes($requestAddress);
            //其他字段赋值
            $province = Address::find($requestAddress['province_id'])->areaname;
            $city = Address::find($requestAddress['city_id'])->areaname;
            $district = Address::find($requestAddress['district_id'])->areaname;
            $street = Street::find($requestAddress['street_id'])->areaname;
            $addressModel->province_name = $province;
            $addressModel->city_name = $city;
            $addressModel->district_name = $district;
            $addressModel->street_name = $street;
            $addressModel->plugins_id = self::PLUGINS_ID;//0商城，31门店，1经销商
            $addressModel->supplier_id = $this->supplier_id;
            $addressModel->uniacid = \YunShop::app()->uniacid;
            //字段检测
            $validator = $addressModel->validator($addressModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //取消其他默认模板
                if($addressModel->is_default){
                    $defaultModel = ReturnAddress::getOneByPluginsId(self::PLUGINS_ID,0,$this->supplier_id);
                    if ($defaultModel) {
                        $defaultModel->is_default = 0;
                        $defaultModel->save();
                    }
                }
                //数据保存
                if ($addressModel->save()) {
                    //显示信息并跳转
                    return $this->message('退货地址创建成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.index'));
                } else {
                    return $this->message('退货地址创建失败');
                }
            }
        }
        return view('Yunshop\Supplier::supplier.return.info', [
            'address' => $addressModel,
        ])->render();
    }

    /**
     * 退货地址编辑
     * @return array $item
     */
    public function edit()
    {
        $addressModel = ReturnAddress::find(\YunShop::request()->id);
        if (!$addressModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }
        $requestAddress = \YunShop::request()->address;

        if ($requestAddress) {
            if (!$requestAddress['province_id']) {
                return $this->message('请选择省份', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.edit',['id' => $addressModel->id]));
            }
            if (!$requestAddress['city_id']) {
                return $this->message('请选择城市', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.edit',['id' => $addressModel->id]));
            }
            if (!$requestAddress['district_id']) {
                return $this->message('请选择区域', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.edit',['id' => $addressModel->id]));
            }
            if (!$requestAddress['street_id']) {
                return $this->message('请选择街道', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.edit',['id' => $addressModel->id]));
            }
            //将数据赋值到model
            $addressModel->setRawAttributes($requestAddress);
            //其他字段赋值
            $province = Address::find($requestAddress['province_id'])->areaname;
            $city = Address::find($requestAddress['city_id'])->areaname;
            $district = Address::find($requestAddress['district_id'])->areaname;
            $street = Street::find($requestAddress['street_id'])->areaname;
            $addressModel->province_name = $province;
            $addressModel->city_name = $city;
            $addressModel->district_name = $district;
            $addressModel->street_name = $street;

            //字段检测
            $validator = $addressModel->validator($addressModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //取消其他默认模板
                if($addressModel->is_default){
                    $defaultModel = ReturnAddress::getOneByPluginsId(self::PLUGINS_ID,0,$this->supplier_id);

                    if ($defaultModel && ($defaultModel->id != \YunShop::request()->id) ) {
                        $defaultModel->is_default = 0;
                        $defaultModel->save();
                    }
                }

                //数据保存
                if ($addressModel->save()) {
                    //显示信息并跳转
                    return $this->message('退货地址更新成功',  Url::absoluteWeb('plugin.supplier.supplier.controllers.address.return-address.index'));
                } else {
                    return $this->message('退货地址更新失败');
                }
            }
        }

        return view('Yunshop\Supplier::supplier.return.info', [
            'address' => $addressModel,
        ])->render();
    }

    /**
     * 退货地址删除
     * @return array $item
     */
    public function delete()
    {
        $address = ReturnAddress::getOne(\YunShop::request()->id);
        if (!$address) {
            return $this->message('无此配送模板或已经删除', '', 'error');
        }

        $model = ReturnAddress::find(\YunShop::request()->id);
        if ($model->delete()) {
            return $this->message('删除模板成功', '');
        } else {
            return $this->message('删除模板失败', '', 'error');
        }
    }

}