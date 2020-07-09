<?php

namespace Yunshop\Diyform\services;
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/23
 * Time: 上午10:52
 */
class DiyformService
{
//    public $_data_type_config = array(0 => '单行文本', 1 => '多行文本', 2 => '下拉框', 3 => '多选框', 4 => '单选框', 5 => '图片', 6 => '身份证号码', 7 => '日期', 8 => '日期范围', 9 => '城市', 10 => '确认文本', 11 => '时间', 12 => '时间范围');
	public $_data_type_config = array(0 => '单行文本', 1 => '多行文本', 2 => '下拉框', 3 => '多选框', 4 => '单选框', 5 => '图片', 7 => '日期', 9 => '城市', 88 => '账号', 99 => '密码');
    public $_default_data_config = array('', '自定义', '姓名', '电话', '微信号');
    public $_default_date_config = array('', '填写当天', '特定日期');

    public function globalData()
    {
        return array('data_type_config' => $this->_data_type_config, 'default_data_config' => $this->_default_data_config, 'default_date_config' => $this->_default_date_config);
    }

    public function getInsertDataByAdmin()
    {
        $tp_type = \YunShop::request()->tp_type;
        $tp_name = \YunShop::request()->tp_name;
        $placeholder = \YunShop::request()->placeholder;
        $tp_is_default = \YunShop::request()->tp_is_default;
        $tp_default = \YunShop::request()->tp_default;
        $tp_must = \YunShop::request()->tp_must;
        $tp_text = \YunShop::request()->tp_text;
        $tp_max = \YunShop::request()->tp_max;
        $tp_name2 = \YunShop::request()->tp_name2;
        $tp_area = \YunShop::request()->tp_area;
        $default_time_type = \YunShop::request()->default_time_type;
        $default_time = \YunShop::request()->default_time;
        $default_btime_type = \YunShop::request()->default_btime_type;
        $default_btime = \YunShop::request()->default_btime;
        $default_etime_type = \YunShop::request()->default_etime_type;
        $default_etime = \YunShop::request()->default_etime;
        $m_pinyin = new PinYinService();

        if (!(empty($tp_name)))
        {
            $data = array();
            $j = 0;
            foreach ($tp_name as $key => $val )
            {
                $i = $m_pinyin->getPinyin($val, 'diy');
                if (array_key_exists($i, $data))
                {
                    $i .= $j;
                    ++$j;
                }
                $temp_tp_type = intval($tp_type[$key]);
                $data[$i]['data_type'] = trim($temp_tp_type);
                $data[$i]['tp_name'] = trim($val);
                $data[$i]['tp_must'] = intval(trim($tp_must[$key]));
                if (($temp_tp_type == 0) || ($temp_tp_type == 1) || ($temp_tp_type == 88) || ($temp_tp_type == 99))
                {
                    if ($temp_tp_type == 0)
                    {
                        $data[$i]['tp_is_default'] = trim($tp_is_default[$key]);
                        if ($data[$i]['tp_is_default'])
                        {
                            $data[$i]['tp_default'] = trim($tp_default[$key]);
                            switch ($data[$i]['tp_is_default'])
                            {
                                case 'diy': $data[$i]['tp_default'] = trim($tp_default[$key]);
                                    break;
                            }
                        }
                    }
                    $data[$i]['placeholder'] = trim($placeholder[$key]);
                }
                else
                {
                    if (($temp_tp_type == 2) || ($temp_tp_type == 3)|| ($temp_tp_type == 4))
                    {
                        $text_array = explode("\n", trim($tp_text[$key]));
                        foreach ($text_array as $k => $v )
                        {
                            $text_array[$k] = trim($v);
                        }
                        $data[$i]['tp_text'] = $text_array;
                    }
                    else if ($temp_tp_type == 5)
                    {
                        $data[$i]['tp_max'] = intval(trim($tp_max[$key]));
                    }
                    else if ($temp_tp_type == 7)
                    {
                        $data[$i]['default_time_type'] = intval($default_time_type[$key]);
                        if ($data[$i]['default_time_type'] == 2)
                        {
                            $data[$i]['default_time'] = trim($default_time[$key]);
                        }
                    }
                    else if ($temp_tp_type == 8)
                    {
                        $data[$i]['default_btime_type'] = intval($default_btime_type[$key]);
                        $data[$i]['default_etime_type'] = intval($default_etime_type[$key]);
                        if ($data[$i]['default_btime_type'] == 2)
                        {
                            $data[$i]['default_btime'] = trim($default_btime[$key]);
                        }
                        if ($data[$i]['default_etime_type'] == 2)
                        {
                            $data[$i]['default_etime'] = trim($default_etime[$key]);
                        }
                    }
                    else if ($temp_tp_type == 9)
                    {
                        $data[$i]['tp_area'] = intval($tp_area[$key]);
                    }
                    else if ($temp_tp_type == 10)
                    {
                        $data[$i]['tp_name2'] = trim($tp_name2[$key]);
                    }
                }
            }
        }
        return $data;
    }
}