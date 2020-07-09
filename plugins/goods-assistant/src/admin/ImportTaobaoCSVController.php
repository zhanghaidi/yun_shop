<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/4/26
 * Time: 16:32
 */

namespace Yunshop\GoodsAssistant\admin;

use app\common\helpers\Url;
use app\common\exceptions\ShopException;
use app\common\components\BaseController;
use Yunshop\GoodsAssistant\services\ImportCSVService;

class ImportTaobaoCSVController extends BaseController
{
    public $excel_name;
    public $zip_name;

    public function taobaoCSV() {

//        $excel_url = Url::shopSchemeUrl(env('APP_URL').'plugins/goods-assistant/storage/examples/test.xlsx');
//        $zip_url = Url::shopSchemeUrl( env('APP_URL').'plugins/goods-assistant/storage/examples/test.zip');

        $excel_url = Url::shopSchemeUrl( \Storage::disk('taobaoCSV')->url('test.xlsx'));
        $zip_url = Url::shopSchemeUrl( \Storage::disk('taobaoCSV')->url('test.zip'));

        $send_data = request()->send;

        if (\Request::isMethod('post')) {
            if (!$send_data['excel_file'] || !$send_data['zip_file']) {
                return $this->message('请上传文件', Url::absoluteWeb('plugin.goods-assistant.admin.importTaobaoCSV.taobaoCSV'), 'error');
            }
            if ($send_data['excel_file']->isValid()) {
                self::uploadExcel($send_data['excel_file']);
            }
            if ($send_data['zip_file']->isValid()) {
                self::uploadZip($send_data['zip_file']);
            }
            $reader = \Excel::load('plugins/goods-assistant/storage/upload'. '/' . $this->excel_name);
            $sheet = $reader->getActiveSheet();//获取当前excel
            $highestRow = $sheet->getHighestRow();//获取excel行数
            $highestColumn = $sheet->getHighestColumn();//获取excel列数
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
            $i = 0;
            $colsIndex = [];

            foreach ($values[0] as $cols => $col) {
                if ($col == 'title') {
                    $colsIndex['title'] = $i;
                }
                if ($col == 'price') {
                    $colsIndex['price'] = $i;
                }
                if ($col == 'num') {
                    $colsIndex['num'] = $i;
                }
                if ($col == 'description') {
                    $colsIndex['description'] = $i;
                }
                if ($col == 'skuProps') {
                    $colsIndex['skuProps'] = $i;
                }
                if ($col == 'picture') {
                    $colsIndex['picture'] = $i;
                }
                if ($col == 'propAlias') {
                    $colsIndex['propAlias'] = $i;
                }
                ++$i;
            }
            $rows = array_slice($values, 2, count($values) - 2);
            $num = 0;
            $s = 0;
            $r = 0;
            $this->get_zip_originalsize($send_data['zip_file'], '../attachment/images/' . \Yunshop::app()->uniacid . '/' . date('Y') . '/' . date('m') . '/');

            foreach ($rows as $rownu => $col) {
                $item = [];
                $item['title'] = $col[$colsIndex['title']];
                $item['price'] = $col[$colsIndex['price']];
                $item['stock'] = $col[$colsIndex['num']];
                $item['content'] = $col[$colsIndex['description']];
                $picContents = $col[$colsIndex['picture']];
                $allpics = explode(';', $picContents);
                $pics = [];
                $optionpics = [];

                foreach ($allpics as $imgurl) {
                    if (empty($imgurl)) {
                        continue;
                    }
                    $picDetail = explode('|', $imgurl);
                    $picDetail = explode(':', $picDetail[0]);
                    $imgurl = request()->getSchemeAndHttpHost() . '/attachment/images/' . \Yunshop::app()->uniacid . '/' . date('Y') . '/' . date('m') . '/' . $picDetail[0] . '.png';
                    if ($imgurl) {
                        if ($picDetail[1] == 1) {
                            $pics[] = $imgurl;
                        }

                        if ($picDetail[1] == 2) {
                            $optionpics[$picDetail[0]] = $imgurl;
                        }
                    }
                }

                $item['pics'] = $pics;
                $res = ImportCSVService::save_taobaocsv_goods($item);
                if ($res) {
                    ++$s;
                } else {
                    ++$r;
                }
                ++$num;
            }
            return $this->message( $s . '个商品导入成功；' . $r . '个失败',Url::absoluteWeb('plugin.goods-assistant.admin.importTaobaoCSV.taobaoCSV'));
        }
        return view('Yunshop\GoodsAssistant::admin.importCSV', [
                'excel_url' => $excel_url,
                'zip_url' => $zip_url,
            ]
        )->render();
    }

    public function uploadExcel($file) {
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        if (!in_array($ext, ['xls', 'xlsx'])) {
            throw new ShopException('不是xls、xlsx文件格式！');
        }
        $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;
//        $fp = fopen(base_path('plugins/goods-assistant/storage/upload/'.$newOriginalName),'w');
//        fwrite($fp,file_get_contents($realPath));
//        fclose($fp);
        \Storage::disk('taobaoCSVupload')->put($newOriginalName, file_get_contents($realPath));

        $this->excel_name = $newOriginalName;
    }

    public function uploadZip($file) {
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        if (!in_array($ext, ['zip'])) {
            throw new ShopException('不是zip文件格式！');
        }

        $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

        $fp = fopen(base_path('plugins/goods-assistant/storage/upload/'.$newOriginalName),'w');
//        fwrite($fp,file_get_contents($realPath));
//        fclose($fp);
        \Storage::disk('taobaoCSVupload')->put($newOriginalName, file_get_contents($realPath));

        $this->zip_name = $newOriginalName;
    }

    public function get_zip_originalsize($filename, $path)
    {
        if (!file_exists($filename)) {
            exit('文件 ' . $filename . ' 不存在！');
        }

        $filename = iconv('utf-8', 'gb2312', $filename);
        $path = iconv('utf-8', 'gb2312', $path);
        $resource = zip_open($filename);
        $i = 1;

        while ($dir_resource = zip_read($resource)) {
            if (zip_entry_open($resource, $dir_resource)) {
                $file_name = $path . zip_entry_name($dir_resource);
                $file_path = substr($file_name, 0, strrpos($file_name, '/'));

                if (!is_dir($file_path)) {
                    mkdir($file_path, 511, true);
                }

                if (!is_dir($file_name)) {
                    $file_size = zip_entry_filesize($dir_resource);

                    if ($file_size < 1024 * 1024 * 10) {
                        $file_content = zip_entry_read($dir_resource, $file_size);
                        $ext = strrchr($file_name, '.');

                        if ($ext == '.png') {
                            file_put_contents($file_name, $file_content);
                        }
                        else {
                            if ($ext == '.tbi') {
                                $file_name = substr($file_name, 0, strlen($file_name) - 4);
                                file_put_contents($file_name . '.png', $file_content);
                            }
                        }
                    }
                }

                zip_entry_close($dir_resource);
            }
        }

        zip_close($resource);
    }
}