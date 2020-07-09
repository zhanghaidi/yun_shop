<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 上午11:03
 */

namespace Yunshop\Supplier\supplier\controllers\apply;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Yunshop\Diyform\models\DiyformDataModel;
use Yunshop\Diyform\models\DiyformTypeModel;
use Yunshop\Supplier\common\models\CoreSetting;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\WeiQingUsers;
use Yunshop\Supplier\common\services\apply\VerifyUserIsApplyService;
use Setting;
use Yunshop\Supplier\common\events\SupplierApplyEvent;

class SupplierApplyController extends ApiController
{
    /**
     * @name 是否开启自定义表单
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function isEnableDiyform()
    {
        $set = Setting::get('plugin.supplier');
        if ($set['diyform_id'] != 0 && app('plugins')->isEnabled('diyform')) {
            $forms = DiyformTypeModel::find($set['diyform_id']);
            if (!$forms) {
                return $this->errorJson('获取数据失败', []);
            }
            $forms->fields = iunserializer($forms->fields);
            $exist_username = false;
            $exist_password = false;
            foreach ($forms->fields as $row) {
                if ($row['data_type'] == 88) {
                    $exist_username = true;
                }
                if ($row['data_type'] == 99) {
                    $exist_password = true;
                }
            }
            if (!$exist_username && !$exist_password) {
                return $this->errorJson('没有账号与密码字段', []);
            }
            return $this->successJson('获取数据成功', ['form_id' => $set['diyform_id']]);
        }
        return $this->errorJson('获取数据失败', []);
    }

    /**
     * @name 验证是否申请是否已经成为供应商
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $result = VerifyUserIsApplyService::verifyUserIsApply(Supplier::getSupplierByMemberId(\YunShop::app()->getMemberId()));
        if (isset($result)) {
            return $this->successJson('失败', [
                'status' => $result
            ]);
        }

        $set = Setting::get('plugin.supplier');

        // 独立框架没有密码强度验证的表
        $settingRes = [];
        if (Schema::hasTable('core_settings')) {
            $settings = CoreSetting::select()->get();
            if (!$settings->isEmpty()) {
                foreach ($settings->toArray() as $value) {
                    $settingRes[$value['key']] = iunserializer($value['value']);
                }
            }
        }

        return $this->successJson('访问成功',[
            'is_open_region' => $set['is_open_region'],
            'safe' => $settingRes['register']['safe'],
            'status'    => VerifyUserIsApplyService::VISIT_SUCCESS,
            'supplier_status' => VerifyUserIsApplyService::VISIT_SUCCESS,
            'signature' => Setting::get('plugin.supplier')['signature']?Setting::get('plugin.supplier')['signature']:'供应商申请！'
        ]);
    }

    /**
     * @name 提交申请
     * @author
     */
    public function apply()
    {
        $form_data_id = intval(request()->form_data_id);
        $apply_data = \YunShop::request()->apply;
        if ($form_data_id) {
           return  $this->addSupplierByFormDataId($form_data_id);
        }
        if ($apply_data) {
            return $this->addSupplierByApplyData($apply_data);
        }
    }

    private function addSupplierByFormDataId($form_data_id)
    {
        $diyform_model = DiyformDataModel::find($form_data_id);
        if (!$diyform_model) {
            throw new AppException('未找到数据');
        }
        $diyform_data = unserialize($diyform_model->data);
        $apply_data = [
            'member_id'         => $diyform_model->member_id,
            'username'          => $diyform_data['diyzhanghao'],
            'password'          => $diyform_data['diymima'],
            'status'            => 0,
            'apply_time'        => time(),
            'uniacid'           => $diyform_model->uniacid,
            'salt'              => str_random(8),
            'diyform_data_id'   => $diyform_model->id,
            'realname'          => '-'
        ];
        if ($diyform_data['diyxingming']) {
            $apply_data['realname'] = $diyform_data['diyxingming'];
        }

        if ($diyform_data['diydizhi']) {
            $arr = explode(' ', $diyform_data['diydizhi']);

            $address = $this->getAddress($arr);

            $apply_data = array_merge($address,$apply_data);

        }

        return $this->addSupplier($apply_data);
    }

    public function getAddress($arr)
    {
        $address = [
            'grade' => 0,
        ];
        if (count($arr) > 1) {
            $address['province_name'] = $arr[0]?:'';
            $address['city_name'] = $arr[1]?:'';
            $address['district_name'] = $arr[2]?:'';

            if (isset($arr[2])) {
                $address['grade'] = 3;
            } elseif (!isset($arr[2]) && isset($arr[1])) {
                $address['grade'] = 2;
            }
        }
        return $address;

    }

    private function addSupplierByApplyData($apply_data)
    {
        if ($apply_data) {
            $apply_data = json_decode($apply_data, true);
            $apply_data['member_id'] = \YunShop::app()->getMemberId();
            $apply_data['status'] = 0;
            $apply_data['apply_time'] = time();
            $apply_data['uniacid'] = \YunShop::app()->uniacid;
            $apply_data['salt'] = str_random(8);
            $apply_data['province_id'] = Address::where('areaname', $apply_data['province_name'])->pluck('id')->first() ?: 0;
            $apply_data['city_id'] = Address::where('areaname', $apply_data['city_name'])->pluck('id')->first() ?: 0;
            $apply_data['district_id'] = Address::where('areaname', $apply_data['district_name'])->pluck('id')->first() ?: 0;

            if (empty($apply_data['district_name']) && !empty($apply_data['city_name'])) {
                $apply_data['grade'] = 2;

            } elseif (!empty($apply_data['district_name'])) {
                $apply_data['grade'] = 3;
            }

          return   $this->addSupplier($apply_data);
        } else {
            return $this->errorJson('请提交申请信息！');
        }
    }

    private function addSupplier($apply_data)
    {
        $supplier_model = new Supplier();
        $validator = $supplier_model->validator($apply_data);
        if ($validator->fails()) {
          //  return $this->errorJson($validator->messages());
            return $this->errorJson('用户名只能由字母和数字组成');
        } else {
            $supplier = Supplier::getSupplierByUsername($apply_data['username']);
            $user = WeiQingUsers::getUserByUserName($apply_data['username'])->first();
            if ($user || $supplier) {
                return $this->errorJson('账号重复！');
            }
            $supplier_model = $supplier_model::create($apply_data);

            event(new SupplierApplyEvent($supplier_model));

            return $this->successJson('提交申请成功，等待审核',[
                'status' => VerifyUserIsApplyService::REPEAT_APPLY
            ]);
        }
    }

    public function upload(Request $request)
    {
        
        // plugin.supplier.supplier.controllers.apply.supplier-apply.upload
        $attach = '';
        $path = $request->file('file')->storeAs('avatars', 'supplier_apply' . str_random(10));

        if (\YunShop::request()->attach) {
            $attach = \YunShop::request()->attach;
        }

        return $this->successJson('上传成功', [
            'img'    => request()->getSchemeAndHttpHost(). config('app.webPath') . '/storage/app/'.$path,
            'attach' => $attach
        ]);
    }
}