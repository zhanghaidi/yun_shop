<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/1
 * Time: 上午10:26
 */

namespace Yunshop\RechargeCode\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\services\ExportService;
use Yunshop\RechargeCode\common\models\RechargeCode;
use Yunshop\RechargeCode\common\services\QrCode;

class ListController extends BaseController
{
    protected $code_model;

    public function __construct()
    {
        $this->code_model = RechargeCode::fetchCodes(request()->search);
    }

    public function index()
    {
        $this->export();
        $this->downloadQrCode();
        $list = $this->code_model->orderBy('id', 'desc')->limit(5)->paginate();
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\RechargeCode::admin.list', [
            'search' => request()->search,
            'list'   => $list,
            'love_name' => trans('Yunshop\Love::love.name'),
            'pager'  => $pager
        ])->render();
    }

    public function delete()
    {
        if (is_array(request()->id)) {
            
            RechargeCode::whereIn('id', request()->id)->delete();

            return json_encode(array('result'=>1, 'msg'=>'删除成功', 'data'=>''));
        } 

        RechargeCode::where('id',request()->id)->delete();
        
        return $this->message('删除成功',Url::absoluteWeb('plugin.recharge-code.admin.list.index'));
    }

    public function export()
    {
        if (request()->export == 1) {
            $export_page = request()->export_page ? request()->export_page : 1;
            $export_model = new ExportService($this->code_model, $export_page);
            if ($export_model->builder_model->isEmpty()) {
                return $this->message('导出数据为空', Url::absoluteWeb('plugin.recharge-code.admin.list.index'), 'error');
            }
            $file_name = date('Ymdhis', time()) . '充值码导出';
            $export_data[0] = ['CODE', '会员', '充值类型', '充值数量', '有效期', '充值状态', '过期状态', '链接'];
            $export_data = $this->setExportData($export_model, $export_data);
            $export_model->export($file_name, $export_data, 'order.list.index');
        }
    }

    public function downloadQrCode()
    {
        if (request()->download == 1) {
            $page = request()->page ?: 1;
            // 第一次执行删除遗留文件，确保存入的文件是当次下载的文件。
            QrCode::firstHandleDelOldQrCode($page);
            list($list, $page_count) = $this->initial($page);
            if ($list->isEmpty()) {
                return $this->message('数据为空', Url::absoluteWeb('plugin.recharge-code.admin.list.index'), 'error');
            }
            $list->map(function($code){
                // 二维码存入临时文件夹
                RechargeCode::setQrCodeToInterim($code->code_key, $code->uid);
            });
            if ($page_count != $page) {
                $this->continueStorage($page, $page_count);
            } else {
                $this->compressAndCreateZip();
            }
        }
    }

    /**
     * @name 设置导出数据
     * @author
     * @param $export_model
     * @param $export_data
     * @return mixed
     */
    private function setExportData($export_model, $export_data)
    {
        foreach ($export_model->builder_model as $key => $item) {
            $export_data[$key + 1] = [
                $item['code_key'],
                $item->hasOneMember->nickname,
                $item['type_name'],
                $item['price'],
                $item['time'],
                $item['bind_name'],
                $item['status_name'],
                yzAppFullUrl('rechargeCodeByQrCode/' . $item['code_key'], ['mid' => $item['uid']])
            ];
        }
        return $export_data;
    }

    /**
     * @name 初始
     * @author
     * @param $page
     * @return array
     */
    private function initial($page)
    {
        $list = $this->code_model->skip(($page - 1) * 10)->take(10)->get();
        $count = $this->code_model->count();
        $page_count = ceil($count / 10);
        $page_count = request()->total ?: $page_count;
        return array($list, $page_count);
    }

    /**
     * @name 继续存储
     * @author
     * @param $page
     * @param $page_count
     */
    private function continueStorage($page, $page_count)
    {
        echo '<div style="border: 6px solid #e0e0e0;width: 12%;margin: 0 auto;margin-top: 12%;padding: 26px 100px;box-shadow: 0 0 14px #a2a2a2;color: #616161;">已完成' . $page . '/' . $page_count . '个。 <div>';
        $page += 1;
        $url = Url::absoluteWeb('plugin.recharge-code.admin.list.index', [
            'search' => request()->search,
            'download' => 1,
            'page' => $page,
            'total' => $page_count
        ]);
        echo '<meta http-equiv="Refresh" content="1; url=' . $url . '" />';
        exit;
    }

    /**
     * @name 压缩并生成zip文件
     * @author
     */
    private function compressAndCreateZip()
    {
        $time = time();
        $filename = storage_path('logs/') . $time . "downqrcode.zip";
        list($fileNameArr, $val) = $this->createZip($filename);
        $this->delOldQrCode($fileNameArr);
        $this->successReturn($time);
    }

    /**
     * @name 创建zip
     * @author
     * @param $filename
     * @return array
     */
    private function createZip($filename)
    {
        $zip = new \ZipArchive();
        if ($zip->open($filename, \ZipArchive::CREATE) !== true) {
            exit ('无法打开文件，或者文件创建失败');
        }
        $fileNameArr = file_tree(storage_path('app/public/interimqr'));
        foreach ($fileNameArr as $val) {
            if (file_exists(storage_path('app/public/interimqr/') . basename($val))) {
                $zip->addFile(storage_path('app/public/interimqr/') . basename($val),
                    basename($val));
            }
        }
        $zip->close();
        return array($fileNameArr, $val);
    }

    /**
     * @name 删除旧的二维码
     * @author
     * @param $fileNameArr
     */
    private function delOldQrCode($fileNameArr)
    {
        foreach ($fileNameArr as $val) {
            file_delete(storage_path('app/public/interimqr/') . basename($val));
        }
    }

    /**
     * @name 成功返回
     * @author
     * @param $time
     */
    private function successReturn($time)
    {
        $url = "http://" . $_SERVER['SERVER_NAME'] . resource_absolute('storage/logs/') . $time . "downqrcode.zip";
        $backurl = "http://" . $_SERVER['SERVER_NAME'] . "/web/index.php?c=site&a=entry&m=yun_shop&do=4302&route=plugin.recharge-code.admin.list.index";
        echo '<div style="border: 6px solid #e0e0e0;width: 12%;margin: 0 auto;margin-top: 12%;padding: 26px 100px;box-shadow: 0 0 14px #a2a2a2;color: #616161;"><a style="color:red;text-decorationnone;"  href="' . $url . '">点击获取下载文件</a><a style="color:#616161"  href="' . $backurl . '">返回</a><div>';
        exit;
    }
}