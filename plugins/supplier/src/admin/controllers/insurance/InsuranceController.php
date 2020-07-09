<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/3
 * Time: 16:30
 */

namespace Yunshop\Supplier\admin\controllers\insurance;

use app\common\components\BaseController;
use app\common\facades\Setting;
use Yunshop\Supplier\common\models\Insurance;
use Yunshop\Supplier\common\models\InsuranceCompany;
use Yunshop\Supplier\common\models\InsurancePdf;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\services\InsuranceService;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class InsuranceController extends BaseController
{

    public function index()
    {
        $set = Setting::get('plugin.supplier');
        $is_company = $set['ins_company_status'];
        $params = \YunShop::request()->get('search');

        $insuranceModel = Insurance::queryData($params);  
        $list = $insuranceModel->orderBy('id', 'desc')->paginate(10)->toArray();
        $list['data'] = InsuranceService::addressTranslation($list['data']);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Supplier::admin.insurance.insurance_list',[
            'data' => $list['data'],
            'pager' => $pager,
            'search' => $params,
            'is_company' => $is_company,
            'phone1' => $set['ins_phone_1'],
            'phone2' => $set['ins_phone_2'],
        ]);
    }


    /**
     * 导出表格
     */
    public function export()
    {
        $params = \YunShop::request()->get('search');
        $export_data[0] = ['序号', '供应商账号','店面名称','被保险人','证件号码','被保险人联系方式','保险详细地址'
            ,'投保财产(需如实填写)','用户类型','保额 （万元）','保险期限 (年)','保费（元）','投保险种（1、火险+盗抢。   2、单独盗抢）','附加玻璃险（35元保1万）份','投保人（安防公司）','保险公司','创建时间','备注'];
        $child = InsuranceService::addressTranslation(Insurance::queryData($params)->get());
        foreach ($child as $key => $item) {
            $insurance = $item;
            $export_data[$key + 1] = [
                $insurance->serial_number,
                $insurance->supplier->username,
                $insurance->shop_name,
                $insurance->insured,
                ' '.$insurance->identification_number,
                $insurance->phone,
                $insurance->address,
                $insurance->insured_property,
                $insurance->customer_type,
                $insurance->insured_amount,
                $insurance->guarantee_period,
                $insurance->premium,
                $insurance->insurance_coverage,
                $insurance->additional_glass_risk,
                $insurance->insurance_company,
                $insurance->created_at,
                $insurance->hasOneCompany->name,
                $insurance->note,
            ];
        }
        \Excel::create('保单数据模板', function ($excel) use ($export_data) {
            $excel->setTitle('Office 2005 XLSX Document');
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");
            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });
        })->export('xls');
    }


    /***
     * 存储保单修改数据
     */
    public function insuranceEdit()
    {
        $is_company = Setting::get('plugin.supplier.ins_company_status');
        $id = \Yunshop::request()->id;
        $data = \Yunshop::request()->data;
        $insurance_model = Insurance::with(['hasOneCompany'])->find($id);

        if ($data){
            $insurance_model->setRawAttributes($data);
            $validator = $insurance_model->validator($insurance_model->getAttributes());

            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($insurance_model->save()) {
                    //显示信息并跳转
                    return $this->message('修改成功',Url::absoluteWeb('plugin.supplier.admin.controllers.insurance.insurance.index'));
                } else {
                    return $this->error('修改失败');
                }
            }
        }

        $company = $list = InsuranceCompany::uniacid()
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        return view('Yunshop\Supplier::admin.insurance.insurance_edit',[
            'data' => $insurance_model,
            'is_company' => $is_company,
            'company_list' => $company,
        ]);
    }

    /**
     * 删除保单
     */
    public function insuranceDel()
    {
        $id = intval(\Yunshop::request()->id);
        $insurance_model = Insurance::find($id);
        if (!$insurance_model) {
            return $this->message('无记录或已被删除', '', 'error');
        }


        if ($insurance_model->delete()) {
            return $this->message('删除成功');
        }
        return $this->message('删除失败', '', 'error');
    }

    public function companyList()
    {
        $params = \YunShop::request()->get('search');

        $list = InsuranceCompany::search($params)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('Yunshop\Supplier::admin.insurance.company_list', [
            'list' => $list,
            'paper' => $pager,
            'search' => $params,
            'sort_url' => 'plugin.supplier.admin.controllers.insurance.insurance.display-order',
        ])->render();
    }

    public function companyAdd()
    {
        $post_data = request()->form;

        if ($post_data) {
            $model = new InsuranceCompany();

            $model->fill($post_data);
            $model->uniacid = \YunShop::app()->uniacid;
            if ($model->save()) {
                return $this->message('保存成功', yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.company-list'), '');
            }
        }

        return view('Yunshop\Supplier::admin.insurance.company_form', [

        ])->render();
    }

    public function companyEdit()
    {
        $post_data = request()->form;
        $id = request()->id;

        if ($post_data) {
            $model = InsuranceCompany::find($id);

            $model->fill($post_data);
            if ($model->save()) {
                return $this->message('修改成功', yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.company-list'), '');
            }
        }

        $company = InsuranceCompany::find($id);

        return view('Yunshop\Supplier::admin.insurance.company_form', [
            'data' => $company,
        ])->render();
    }

    public function companyDel()
    {
        $id = request()->id;
        $company = InsuranceCompany::find($id);

        if ($company->delete()) {
            return $this->message('删除成功');
        }
    }

    public function changeOpen()
    {
        $id = (int)request()->id;
        $company = InsuranceCompany::find($id);
        $company->is_show = 1;

        if ($company->save()) {
            return $this->successJson('开启显示');
        } else {
            return $this->errorJson('开启失败');
        }
    }

    public function changeClose()
    {
        $id = (int)request()->id;
        $company = InsuranceCompany::find($id);
        $company->is_show = 0;

        if ($company->save()) {
            return $this->successJson('关闭显示');
        } else {
            return $this->errorJson('关闭失败');
        }
    }

    public function displayOrder()
    {
        $displayOrders = request()->display_order;

        foreach($displayOrders as $id => $displayOrder){
            $company = InsuranceCompany::find($id);
            $company->sort = $displayOrder ? $displayOrder : 0;

            $company->save();
        }

        return $this->message('排序成功');
    }

    public function upload()
    {
        $post_data = request()->form;

        if ($post_data) {
            $files = json_encode($post_data['file']);
            foreach ($post_data['supplier_id'] as $s_id) {
                $model = new InsurancePdf(['supplier_id' => $s_id]);
                $model->pdf = $files;
                $model->save();
            }

            return $this->successJson('保存成功');
        }

        return view('Yunshop\Supplier::admin.insurance.upload', [
            'data'
        ])->render();
    }

    public function uploadPdf()
    {
        $file = request()->file('file');

        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }

        if (!$file->isValid()) {
            return $this->errorJson('上传失败.');
        }

        // 获取文件相关信息
        $originalName = $file->getClientOriginalName(); // 文件原名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $ext = $file->getClientOriginalExtension(); //文件后缀

        if ($ext != 'pdf') {
            return $this->errorJson('请上传pdf文件');
        }

        if (strexists($originalName, '.pdf')) {
            $originalName = mb_substr($originalName, 0, mb_strpos($originalName, '.p'));
        }

        $newOriginalName = $originalName . md5(str_random(6)) . '.' . $ext;

        $result = \Storage::disk('insurance')->put($newOriginalName, file_get_contents($realPath));

        if (!$result) {
            return $this->successJson('上传失败');
        }

        if (config('app.framework') == 'platform') {
            $url = request()->getSchemeAndHttpHost() . \Storage::disk('insurance')->url($newOriginalName);
        } else {
            $url = request()->getSchemeAndHttpHost() . '/addons/yun_shop' . \Storage::disk('insurance')->url($newOriginalName);
        }

        return $this->successJson('上传成功', [
            'file_name' => $newOriginalName,
            'url' => $url,
        ]);
    }

    public function searchSupplierByName()
    {
        $keyword = request()->keyword;
        $models = Supplier::select('id', 'username', 'member_id', 'realname')
            ->with(['hasOneMember' => function ($q) {
                $q->select(['uid', 'avatar']);
            }])
            ->whereHas('hasOneWqUser', function ($q) use ($keyword) {
                $q->where('username', 'like', '%' . $keyword . '%');
            })
            ->get();

        foreach ($models as &$model) {
            $model->avatar = $model->hasOneMember->avatar;
        }

        return $this->successJson('ok', [
            'supplier' => $models,
        ]);
    }

    public function getSearchInsCompany()
    {
        $keyword = request()->keyword;
        $company = InsuranceCompany::select('id', 'name')
            ->where('name', 'like', '%' . $keyword . '%')
            ->get();

        return view('Yunshop\Supplier::admin.insurance.company_query', [
            'company' => $company,
        ])->render();
    }
}