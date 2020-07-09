<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/23
 * Time: 下午3:50
 */

namespace Yunshop\Printer\admin;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Printer\common\models\Printer;

class ListController extends BaseController
{
    private $printer_model;

    public function index()
    {
        $kwd = trim(request()->kwd);
        $list = Printer::fetchPrints($kwd)->orderBy('id', 'desc')->paginate();
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Printer::admin.printer_list', [
            'list' => $list,
            'pager' => $pager,
            'kwd' => $kwd
        ])->render();
    }

    public function add()
    {
        $printer_data = request()->printer;
        $printer_owner = \app\common\modules\shop\ShopConfig::current()->get('printer_owner');
        if ($printer_data) {
            $printer_data['uniacid'] = \YunShop::app()->uniacid;
            $printer_data['owner'] = $printer_owner['owner'];
            $printer_data['owner_id'] = $printer_owner['owner_id'];

            $ret = Printer::add($printer_data);
            if (!$ret) {
                return $this->message('添加打印机成功', Url::absoluteWeb('plugin.printer.admin.list.index'));
            }
            $this->error($ret);
        }

        return view('Yunshop\Printer::admin.printer_detail', [

        ])->render();
    }

    public function edit()
    {
        $this->exception();
        $printer_data = request()->printer;
        if ($printer_data) {
            $ret = Printer::edit($printer_data, $this->printer_model);
            if (!$ret) {
                return $this->message('修改打印机成功', Url::absoluteWeb('plugin.printer.admin.list.index'));
            }
            $this->error($ret);
        }

        return view('Yunshop\Printer::admin.printer_detail', [
            'printer' => $this->printer_model->toArray()
        ])->render();
    }

    public function del()
    {
        $this->exception();
        $this->printer_model->delete();
        return $this->message('删除打印机成功', Url::absoluteWeb('plugin.printer.admin.list.index'));
    }

    public function changeStatus()
    {
        $id = request()->id;
        $field = request()->type;
        $data = (request()->data == 1 ? '0' : '1');
        $printer = Printer::getPrinterById($id)->first();
        $printer->$field = $data;
        $printer->save();
        echo json_encode(["data" => $data, "result" => 1]);
    }

    private function exception()
    {
        $id = intval(request()->id);
        if (!$id) {
            throw new ShopException('参数错误');
        }
        $printer = Printer::getPrinterById($id)->first();
        if (!$printer) {
            throw new ShopException('未找到打印机');
        }
        $this->printer_model = $printer;
    }
}