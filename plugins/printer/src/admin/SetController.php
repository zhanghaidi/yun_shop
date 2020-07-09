<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/23
 * Time: 下午3:55
 */

namespace Yunshop\Printer\admin;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Printer\common\models\Printer;
use Yunshop\Printer\common\models\PrintSet;
use Yunshop\Printer\common\models\Temp;

class SetController extends BaseController
{
    private $print_set;

    /**
     * SetController constructor.
     */
    public function __construct()
    {
        $this->print_set = PrintSet::fetchSetting();
        if (!$this->print_set) {
            $this->print_set = new PrintSet();
        }
    }

    public function index()
    {
        $printer_owner = \app\common\modules\shop\ShopConfig::current()->get('printer_owner');
        $printers = Printer::fetchPrints('')->get();
        $temps = Temp::fetchTemps('')->get();
        $print_set = PrintSet::fetchSetting();
        if (request()->isMethod('post')) {
            $set_data = request()->setdata;
            if (!$set_data['print_type']) {
                $set_data['print_type'] = [];
            }
            $set_data['uniacid'] = \YunShop::app()->uniacid;
            $set_data['owner'] = $printer_owner['owner'];
            $set_data['owner_id'] = $printer_owner['owner_id'];
            $this->print_set->fill($set_data);
            $this->print_set->save();
            return $this->message('保存打印设置成功', Url::absoluteWeb('plugin.printer.admin.set.index'));
        }

        return view('Yunshop\Printer::admin.set', [
            'printers' => $printers,
            'temps' => $temps,
            'print_set' => $print_set,
            'owner' => $printer_owner['owner']
        ])->render();
    }
}