<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/23
 * Time: 上午11:20
 */

namespace Yunshop\Diyform\admin;


class DiyformTplController extends DiyformController
{

    public function getFormTpl()
    {
        $data_type_config = [];
        $default_data_config = [];
        $default_date_config = [];
        extract($this->globalData);
        $addt = \YunShop::request()->addt;
        $kw = \YunShop::request()->kw;
        $flag = intval(\YunShop::request()->flag);
        $data_type = \YunShop::request()->data_type;
        $tmp_key = $kw;

        return view('Yunshop\Diyform::admin.tpl.tpl', [
            'data_type_config' => $data_type_config,
            'default_data_config' => $default_data_config,
            'default_date_config' => $default_date_config,
            'addt' => $addt,
            'kw' => $kw,
            'flag' => $flag,
            'data_type' => $data_type,
            'tmp_key' => $tmp_key,
        ])->render();
    }

}