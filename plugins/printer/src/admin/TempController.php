<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/23
 * Time: 下午3:54
 */

namespace Yunshop\Printer\admin;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Printer\common\models\Temp;

class TempController extends BaseController
{
    private $temp_model;

    public function index()
    {
        $list = Temp::fetchTemps(trim(request()->kwd))->orderBy('id', 'desc')->paginate();
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Printer::admin.temp_list', [
            'list' => $list,
            'pager' => $pager,
            'kwd' => trim(request()->kwd)
        ])->render();
    }

    public function add()
    {
        $printer_owner = \app\common\modules\shop\ShopConfig::current()->get('printer_owner');
        if (request()->isMethod('post')) {
            $print_data = \YunShop::request()->temp;
            $print_data['uniacid'] = \YunShop::app()->uniacid;
            $print_data['owner'] = $printer_owner['owner'];
            $print_data['owner_id'] = $printer_owner['owner_id'];
            $temp_model = new Temp();
            $temp_model->fill($print_data);
            $validator = $temp_model->validator();
            if (!$validator->fails()) {
                $temp_model->save();
                return $this->message('添加模板成功', Url::absoluteWeb('plugin.printer.admin.temp.index'));
            }
            $this->error($validator->messages());
        }

        return view('Yunshop\Printer::admin.temp_detail', [
            'kw' => 0
        ])->render();
    }

    /**
     * @return mixed|string
     * @throws ShopException
     * @throws \Throwable
     */
    public function edit()
    {
        $this->exception();
        if (request()->isMethod('post')) {
            $print_data = \YunShop::request()->temp;
            $this->temp_model->fill($print_data);
            $validator = $this->temp_model->validator();
            if (!$validator->fails()) {
                $this->temp_model->save();
                return $this->message('修改模板成功', Url::absoluteWeb('plugin.printer.admin.temp.index'));
            }
            $this->error($validator->messages());
        }
        return view('Yunshop\Printer::admin.temp_detail', [
            'temp' => $this->temp_model,
            'kw' => 0
        ])->render();
    }

    /**
     * @return mixed
     * @throws ShopException
     */
    public function del()
    {
        $this->exception();
        $this->temp_model->delete();
        return $this->message('删除模板成功', Url::absoluteWeb('plugin.printer.admin.temp.index'));
    }

    /**
     * @throws ShopException
     */
    private function exception()
    {
        $id = intval(request()->id);
        if (!$id) {
            throw new ShopException('参数错误');
        }
        $temp_model = Temp::getTempById($id)->first();
        if (!$temp_model) {
            throw new ShopException('未找到模板数据');
        }
        $this->temp_model = $temp_model;
    }

    public function tpl()
    {
        $kw = intval(request()->kw);
        return view('Yunshop\Printer::admin.tpl', [
            'kw' => $kw
        ])->render();
    }
}