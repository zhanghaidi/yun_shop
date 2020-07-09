<?php

namespace Yunshop\Supplier\supplier\controllers\insurance;


use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\models\Address;
use app\common\models\Street;
use app\common\services\Session;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\common\models\Insurance;

class BatchsendController extends SupplierCommonController
{
    private $originalName;
    private $reader;
    private $count = 0;
    private $success_num = 0;
    private $err_array = [];
    private $error_msg;

    public function __construct()
    {
        // 生成目录
        if (!is_dir(storage_path('app/public/orderexcel'))) {
            mkdir(storage_path('app/public/orderexcel'), 0777);
        }
    }

    public function index()
    {
        $send_data = request()->send;;
        if (\Request::isMethod('post')) {

            if (!$send_data['excelfile']) {
                return $this->message('请上传文件', 'error');
            }
            if ($send_data['excelfile']->isValid()) {
                $this->uploadExcel($send_data['excelfile']);
                $this->readExcel();
                $this->handleInsurance($this->getRow(), $send_data);

            }
        }

        return view('Yunshop\Supplier::supplier.insurance.successful_import',[
            'count'          =>  $this->count,
            'err_count'      => count($this->err_array),
            'success_num'    => $this->success_num,
            'err_data'       => $this->err_array
        ]);
    }

    /**
     * @name 保存excel文件
     * @author
     * @param $file
     * @throws ShopException
     */
    private function uploadExcel($file)
    {
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        if (!in_array($ext, ['xls', 'xlsx'])) {
            throw new ShopException('不是xls、xlsx文件格式！');
        }

        $newOriginalName = md5($originalName . str_random(6)) . $ext;
        \Storage::disk('orderexcel')->put($newOriginalName, file_get_contents($realPath));

        $this->originalName = $newOriginalName;
    }

    /**
     * @name 读取文件
     * @author
     */
    private function readExcel()
    {
        $this->reader = \Excel::load(storage_path('app/public/orderexcel') . '/' . $this->originalName);
    }

    /**
     * @name 获取表格内容
     * @author
     * @return array
     */
    private function getRow()
    {
        $values = [];
        $sheet = $this->reader->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnCount = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $row = 2;
        while ($row <= $highestRow)
        {
            $rowValue = array();
            $col = 0;
            while ($col < $highestColumnCount)
            {
                $rowValue[] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
                ++$col;
            }
            $values[] = $rowValue;
            ++$row;
        }
        return $values;
    }

    /**
     * @name 订单发货
     * @author
     * @param $values
     * @param $send_data
     */
    private function handleInsurance($values, $send_data)
    {
        $this->count = count($values);
        foreach ($values as $rownum => $col) {
            /**
             *   0 => "序号"
            1 => "店面名称"
            2 => "被保险人"
            3 => "证件号码"
            4 => "被保险人联系方式"
            5 => "保险地址  "
            6 => "投保财产"
            7 => "用户类型"
            8 => "保额"
            9 => "保期"
            10 => "保费"
            11 => "保险公司"
            12 => "备注"
             */

            if (($col[0] == '' || $col[0] == null) && ($col[1] == '' || $col[1] == null) && ($col[2] == '' || $col[2] == null) &&
                ($col[3] == '' || $col[3] == null) && ($col[4] == '' || $col[4] == null) && ($col[5] == '' || $col[5] == null) &&
                ($col[6] == '' || $col[6] == null) && ($col[7] == '' || $col[7] == null) && ($col[8] == '' || $col[8] == null) &&
                ($col[9] == '' || $col[9] == null) && ($col[10] == '' || $col[10] == null) && ($col[11] == '' || $col[11] == null) &&
                ($col[12] == '' || $col[12] == null) && ($col[13] == '' || $col[13] == null) && ($col[14] == '' || $col[14] == null) ){
                continue;
            }


            /*
            $insurance_model = new Insurance();
            $insurance_model->uniacid = \Yunshop::app()->uniacid;
            $insurance_model->supplier_id =Session::get('supplier')['id'];
            $insurance_model->serial_number = $col[0];  //序号
            $insurance_model->shop_name = $col[1];      //('店面名称');
            $insurance_model->insured = $col[2];        //('被保人');
            $insurance_model->identification_number = $col[3]; //('证件号码');
            $insurance_model->phone = $col[4];          //('联系方式');
            $insurance_model->address = $col[5];       //('详细地址');
            $insurance_model->insured_property = $col[6];        //('投保财产');
            $insurance_model->customer_type = $col[7];           //('投保类型');
            $insurance_model->insured_amount = $col[8];         //('保额');
            $insurance_model->guarantee_period = $col[9];       //('保期');
            $insurance_model->premium = $col[10];                //('保费');
            $insurance_model->insurance_coverage = $col[11];      //('投保险种');
            $insurance_model->additional_glass_risk = $col[12];      //('附加玻璃险');
            $insurance_model->insurance_company = $col[13];      //('保险公司');
            $insurance_model->note = $col   [14];                   //('备注');
             */

            $insurance_model = new Insurance();
            $insurance_model->uniacid = \Yunshop::app()->uniacid;
            $insurance_model->supplier_id =Session::get('supplier')['id'];
            $insurance_model->serial_number = $col[0];  //序号
            $insurance_model->shop_name = $col[1];      //('店面名称');
            $insurance_model->insured = $col[2];        //('被保人');
            $insurance_model->identification_number = $col[3]; //('证件号码');
            $insurance_model->phone = $col[4];          //('联系方式');
            $insurance_model->address = $col[5];       //('详细地址');
            $insurance_model->insured_property = $col[6];        //('投保财产');
            $insurance_model->customer_type = $col[7];           //('投保类型');
            $insurance_model->insured_amount = $col[8];         //('保额');
            $insurance_model->guarantee_period = $col[9];       //('保期');
            $insurance_model->premium = $col[10];                //('保费');
            $insurance_model->insurance_coverage = $col[11];      //('投保险种');
            $insurance_model->additional_glass_risk = $col[12];      //('附加玻璃险');
            $insurance_model->insurance_company = $col[13];      //('保险公司');
            $insurance_model->note = $col   [14];                   //('备注');
            if (!$insurance_model->save()){
                $this->err_array[] = '序号：'.$col[0].'  - >   店面名称：'.$col[1];
                continue;
            }
            $this->success_num += 1;
        }
        $this->setErrorMsg();
    }


    /**
     * @name 设置错误信息
     * @author
     */
    private function setErrorMsg()
    {
        if (count($this->err_array) > 0) {
            $num = 1;
            $this->error_msg = '<br>' . count($this->err_array) . '条数据导入失败，失败数据信息: <br>';
//            dd($this->err_array);
//            foreach ($this->err_array as $k => $v )
//            {
//                $this->error_msg .= $v . ' ';
//                if (($num % 2) == 0)
//                {
//                    $this->error_msg .= '<br>';
//                }
//                ++$num;
//            }
        }
    }


    /**
     * @name 获取示例excel
     * @author
     */
    public function getexample()
    {
        $export_data[0] = ["订单编号", "快递单号"];
        \Excel::create('批量发货数据模板', function ($excel) use ($export_data) {
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
}