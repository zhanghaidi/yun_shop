<?php

namespace Yunshop\Diyform\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Diyform\models\DiyformDataModel;
use Yunshop\Diyform\models\DiyformTypeModel;

class DiyformDataController extends BaseController
{
    public $pageSize = 20;
    public $formId;
    public $forms;
    public $memberId;
    public $formType;

    public function __construct()
    {
        $this->formDataId = \YunShop::request()->form_data_id;
        $this->formId = \YunShop::request()->id;
        $this->forms = DiyformTypeModel::find($this->formId);

        $this->memberId = \YunShop::request()->member_id;
        $this->formType = \YunShop::request()->form_type;

    }

    /**
     * 获取自定义表单数据
     */
    public function getMemberFormData()
    {
        $list = DiyformDataModel::getDiyFormDataByFormId($this->formId, $this->formDataId,$this->memberId,$this->formType)->orderBy('id', 'desc')->paginate($this->pageSize);

        if ($list) {
            //dd($memberData[0]);exit;
            $this->forms = DiyformTypeModel::find($list[0]->form_id);
        }

        return $this->render($list);

    }

    /**
     * 获取自定义表单数据
     */
    public function getFormData()
    {
        $list = DiyformDataModel::getDiyFormDataByFormId($this->formId, $this->formDataId,$this->memberId,$this->formType)
            ->with('member')
            ->orderBy('id', 'desc')
            ->paginate($this->pageSize);
        return $this->render($list);
    }

    /**
     * 获取自定义表单数据
     */
    public function render($list)
    {

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        $fields = iunserializer($this->forms->fields);
        return view('Yunshop\Diyform::admin.diyform-data-list', [
            'items' => $list,
            'pager' => $pager,
            'fields' => $fields,
            'formId' => $this->formId,
            'formDataId' => $this->formDataId,
        ])->render();

    }

    /**
     * 获取自定义表单数据
     */
    public function getFormDataDetail()
    {
        $detail = DiyformDataModel::where('id', $this->formDataId)->first();

        if ($detail) {
            $this->forms = DiyformTypeModel::find($detail->form_id);
        }

        $fields = iunserializer($this->forms->fields);

//        dd($fields, $detail->form_data);

        return view('Yunshop\Diyform::admin.diyform-data-detail', [
            'item' => $detail,
            'fields' => $fields,
            'formId' => $this->forms->id,
        ])->render();
    }


    /**
     * 导出自定义表单数据
     */
    public function export()
    {
        $list = DiyformDataModel::getDiyFormDataByFormId($this->formId, $this->formDataId)->orderBy('id', 'desc')
            ->get()
            ->toArray();
        $file_name = date('Ymdhis', time()) . $this->forms->title . '-导出';
        $fields = iunserializer($this->forms->fields);
        foreach ($fields as $field) {
            $export_data[0][] = $field['tp_name'];
        }
        foreach ($list as $keys => $item) {
            foreach ($fields as $fname => $field) {
                foreach ($item['form_data'] as $key => $val) {
                    if ($key == $fname) {
                        if ($field['data_type'] == 5){
                            foreach ($val as $k => $v) {
                                $data[$k] = yz_tomedia($val[$k]);
                            }
                            $data = implode(',',$data);
                            $export_data[$keys + 1][] = $data;
                            $data = array();
                        }else{
                            $export_data[$keys + 1][] = $val;
                        }
                    }
                }
            }
        }

        \Excel::create($file_name, function ($excel) use ($export_data) {
            // Set the title
            $excel->setTitle('Office 2005 XLSX Document');

            // Chain the setters
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


}