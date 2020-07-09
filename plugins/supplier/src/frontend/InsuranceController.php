<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/8
 * Time: 10:18
 */

namespace Yunshop\Supplier\frontend;



use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\facades\Setting;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\models\Insurance;
use Yunshop\Supplier\common\models\InsuranceCompany;
use Yunshop\Supplier\common\services\InsuranceService;


class InsuranceController extends ApiController
{
    /**
     * 个人中心表单列表
     */
    public function index()
    {
        $set = Setting::get('plugin.supplier');
        $member_id = \YunShop::app()->getMemberId();

        $memberSupplier = Supplier::uniacid()->where('member_id',$member_id)->first();
        if (empty($memberSupplier)) {
            return $this->errorJson('没有权限,跳转供应商申请!', ['url'=> yzAppFullUrl('member/supplier')]);
        }

        $data = Insurance::memberInsurance($member_id);

        return $this->successJson('ok', [
            'list' => $data,
            'set' => $set,
        ]);
    }

    public function insuranceDetail()
    {
       $id = \Yunshop::request()->id;
       $data = Insurance::with(['hasOneCompany'])->find($id);

       if (!$data){
          return $this->errorJson('找不到保单记录');
       }

       return $this->successJson('ok', $data);
    }

    /***
     * 存储保单修改数据
     */
    public function insuranceEdit()
    {
        $id = \Yunshop::request()->id;
        $data = \Yunshop::request()->data;
        $insurance_model = Insurance::with(['hasOneCompany'])->find($id);

        if ($data){
            $insurance_model->fill($data);

            if ($insurance_model->save()) {
                //显示信息并跳转
                return $this->successJson('修改成功');
            } else {
                return $this->errorJson('修改失败');
            }
        }

        if ($insurance_model){
            return $this->successJson('查询数据成功',$insurance_model);
        }else{
            return $this->errorJson('找不到数据');
        }

    }

    /***
     * 存储保单添加数据
     */
    public function insuranceAdd()
    {
        $data = \Yunshop::request()->data;

        if ($data){
            $memberSupplier = Supplier::uniacid()->where('member_id',\YunShop::app()->getMemberId())->first();
            $data['supplier_id'] = $memberSupplier->id;
            $data['uniacid'] = \Yunshop::app()->uniacid;
            $insurance = new Insurance();

            $insurance->fill($data);
            //数据保存
            if ($insurance->save()) {
                return $this->successJson('添加成功');
            } else {
                return $this->errorJson('添加失败');
            }
        }else{
            return $this->errorJson('数据为空，添加失败');
        }
    }

    /**
     * 删除保单
     */
    public function insuranceDel()
    {
        $id = intval(\Yunshop::request()->id);
        $insurance_model = Insurance::find($id);
        if (!$insurance_model) {
            return $this->errorJson('无记录或已被删除');
        }

        if ($insurance_model->delete()) {
            return $this->successJson('删除成功');
        }

        return $this->errorJson('删除失败', '', 'error');
    }

    public function searchCompany()
    {
        $kwd = request()->kwd;

        $company = InsuranceCompany::select('id', 'name')
            ->where('name', 'like', '%' . $kwd . '%')
            ->where('is_show', 1)
            ->paginate(20);

        $is_company = Setting::get('plugin.supplier.ins_company_status');
        if ($is_company != 1) {
            return $this->errorJson('保险公司开关已关闭，请到后台打开');
        }

        return $this->successJson('ok', $company);
    }

    public function contInsurance()
    {
        $id = request()->ins_id;

        $insurance = Insurance::find($id);
        if (!$insurance) {
            return $this->errorJson('保单不存在或已被删除');
        }

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'supplier_id' => $insurance->supplier_id,
            'serial_number' => $insurance->serial_number,
            'shop_name' => $insurance->shop_name,
            'insured' => $insurance->insured,
            'identification_number' => $insurance->identification_number,
            'phone' => $insurance->phone,
            'province_id' => $insurance->province_id,
            'city_id' => $insurance->city_id,
            'district_id' => $insurance->district_id,
            'street_id' => $insurance->street_id,
            'address' => $insurance->address,
            'insured_property' => $insurance->insured_property,
            'customer_type' => $insurance->customer_type,
            'insured_amount' => $insurance->insured_amount,
            'guarantee_period' => $insurance->guarantee_period,
            'premium' => $insurance->premium,
            'insurance_coverage' => $insurance->insurance_coverage,
            'additional_glass_risk' => $insurance->additional_glass_risk,
            'insurance_company' => $insurance->insurance_company,
            'note' => $insurance->note,
            'company_id' => $insurance->company_id,
        ];

        $model = new Insurance();
        $model->fill($data);
        if ($model->save()) {
            return $this->successJson('续保成功');
        }
    }
}