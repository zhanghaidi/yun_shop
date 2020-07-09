<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/27
 * Time: 上午11:24
 */

namespace Yunshop\Supplier\admin\controllers\set;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;
use Yunshop\Diyform\models\DiyformTypeModel;

class SetController extends BaseController
{
    public function index()
    {
        $set = Setting::get('plugin.supplier');
        $set_data = \YunShop::request()->setdata;

        if ($set_data) {
            $set_data['service_money'] = intval($set_data['service_money']);
            $set_data['service_type'] = $set_data['service_type'] ? $set_data['service_type'] : 0;
            if (!is_numeric($set_data['apply_day']) || !is_numeric($set_data['limit_day'])) {
                $this->error('提现限制和订单完成天数请填写整数');
            } else {
                $set_data['apply_day'] = intval($set_data['apply_day']);
                $set_data['limit_day'] = intval($set_data['limit_day']);
                if (Setting::set('plugin.supplier', $set_data)) {
                    return $this->message('设置成功', Url::absoluteWeb('plugin.supplier.admin.controllers.set.set'));
                } else {
                    $this->error('设置失败');
                }
            }
        }

        $temp_list = MessageTemp::getList();

        return view('Yunshop\Supplier::admin.set.index', [
            'set' => $set,
            'var' => \YunShop::app()->get(),
            'exist_diyform' => app('plugins')->isEnabled('diyform'),
            'diyform_list' => $this->getDiyformList(),
            'temp_list' => $temp_list
        ])->render();
    }

    /**
     * @name 获取自定义表单列表
     * @author
     * @return mixed
     */
    private function getDiyformList()
    {
        $exist_diyform = app('plugins')->isEnabled('diyform');
        if ($exist_diyform) {
            $diyform_list = DiyformTypeModel::getDiyformList()->orderBy('id', 'desc')->get();
            return $this->filterDiyformList($diyform_list);
        }
    }

    /**
     * @name 只保留有账号密码字段的自定义表单数据
     * @author
     * @param $diyform_list
     * @return mixed
     */
    private function filterDiyformList($diyform_list)
    {
        if (!$diyform_list->isEmpty()) {
            return $diyform_list->filter(function ($diyform) {
                $fields = unserialize($diyform->fields);
                foreach ($fields as $row) {
                    if ($row['data_type'] == 88) {
                        $exist_username = true;
                    }
                    if ($row['data_type'] == 99) {
                        $exist_password = true;
                    }
                }
                return $exist_username == true && $exist_password == true;
            });
        }
    }
}