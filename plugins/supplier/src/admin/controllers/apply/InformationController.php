<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/8
 * Time: 下午5:34
 */

namespace Yunshop\Supplier\admin\controllers\apply;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Diyform\models\DiyformDataModel;
use Yunshop\Diyform\models\DiyformTypeModel;
use Yunshop\Supplier\admin\models\Supplier;

class InformationController extends BaseController
{
    public function getInfoByFormId()
    {
        if (!app('plugins')->isEnabled('diyform')) {
            return $this->message('自定义表单插件已关闭,无法查看', Url::absoluteWeb('plugin.supplier.admin.controllers.apply.supplier-apply.index'), 'error');
        }
        $member_id = intval(request()->member_id);
        $form_data_id = intval(request()->form_data_id);
        $form_data = DiyformDataModel::select()->whereId($form_data_id)->whereMemberId($member_id)->whereUniacid(\YunShop::app()->uniacid)->first();
        $supplier = Supplier::getSupplierById(intval(request()->supplier_id));
        $type_model = DiyformTypeModel::find($form_data->form_id);
        $fields = unserialize($type_model->fields);
        return view('Yunshop\Supplier::admin.apply.diyform', [
            'form_data' => $form_data->form_data,
            'supplier' => $supplier,
            'fields' => $fields
        ])->render();
    }
}