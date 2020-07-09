<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/24
 * Time: 下午4:06
 */

namespace Yunshop\Printer\common\services;

use Ixudra\Curl\Facades\Curl;
use Yunshop\Printer\common\models\Printer;
use Yunshop\Printer\common\models\PrintLog;
use Yunshop\Printer\common\models\PrintSet;
use Yunshop\Printer\common\models\Temp;

class PrintingService
{
    private $order;
    private $printer_model;
    private $temp_model;
    private $print_type;
    private $code;
    const API_NAME = 'Open_printMsg';
    const API = 'http://api.feieyun.cn/Api/Open/';


    /**
     * PrintingService constructor.
     * @param $order
     * @param $print_type
     * @param $code
     *
     */
    public function __construct($order, $print_type, $code)
    {
        \Log::info('print_type',$print_type);
        $this->order = $order;
        $this->print_type = $print_type;
        $this->code = $code;
        \Log::info('owner:',  \app\common\modules\shop\ShopConfig::current()->get('printer_owner'));
        $printer_owner = \app\common\modules\shop\ShopConfig::current()->get('printer_owner');
        $printSet = PrintSet::where('owner',$printer_owner['owner'])->where('owner_id',$printer_owner['owner_id'])->first();
        if($this->verify($printSet)){
            $this->handle();
        }
        return true;
    }

    /**
     * @name 执行
     * @author
     */
    public function handle()
    {
        list($content, $style, $len) = $this->getStyle();
        $content = $this->getContent($style, $content, $len);
        $self_message = $this->getSendMessage($content);
        Curl::to(self::API)->withData($self_message)->post();
        $this->addPrintedLog($self_message);
    }

    /**
     * @param $self_message
     */
    private function addPrintedLog($self_message)
    {
        PrintLog::create([
            'uniacid' => \YunShop::app()->uniacid,
            'print_id' => $this->printer_model->id,
            'temp_id' => $this->temp_model->id,
            'temp_id' => $this->temp_model->id,
            'order_id' => $this->order->id,
            'code' => $this->code
        ]);
    }

    /**
     * @name 验证
     * @author
     * @return bool
     */
    private function verify($print_set)
    {
        \Log::info('打印设置:' .  $print_set);
        if (!$print_set) {
            return false;
        }
        \Log::info('打印类型，print_type:' . $this->print_type . '/arr:', $print_set->print_type);
        if (!in_array($this->print_type, $print_set->print_type)) {
            \Log::info('未开启打印');
            return false;
        }


        if (!$print_set['temp_id'] || !$print_set['printer_id']) {
            return false;
        }
        $printer_model = Printer::getPrinterById($print_set['printer_id'])->first();
        if (!$printer_model) {
            return false;
        }
        if (!$printer_model->status) {
            return false;
        }
        $temp_model = Temp::getTempById($print_set['temp_id'])->first();
        if (!$temp_model) {
            return false;
        }

        $this->printer_model = $printer_model;
        $this->temp_model = $temp_model;
        return true;
    }

    /**
     * @name 替换变量
     * @author
     * @param array $array
     * @param $message
     * @return mixed
     */
    private function replaceArray(array $array, $message)
    {
        foreach ($array as $key => $value ) {
            $message = str_replace($key, $value, $message);
        }
        return $message;
    }

    /**
     * @name 空格补齐
     * @author
     * @param $str
     * @param int $length
     * @return string
     */
    private function setSpacing($str, $length = 32)
    {
        $str_old = $str;
        $str = (($this->is_utf8($str) ? iconv('utf-8', 'gb2312', $str) : $str));
        $num = strlen($str);
        if ($length < $num) {
            if ((32 < $num) && ($length == 32)) {
                $temp = '';
                $count = ceil($num / $length);
                $i = 0;
                while ($i <= $count)
                {
                    $temp .= mb_substr($str_old, $i * $length, $length);
                    ++$i;
                }
                return $temp;
            }
            return mb_substr($str_old, 0, floor($length / 2), 'utf-8') . str_repeat(' ', $length % 2);
        }
        return $str_old . str_repeat(' ', $length - $num);
    }

    /**
     * @name 验证是否utf8
     * @author
     * @param $str
     * @return bool
     */
    private function is_utf8($str){
        $len = strlen($str);
        for($i = 0; $i < $len; $i++){
            $c = ord($str[$i]);
            if ($c > 128) {
                if (($c > 247)) return false;
                elseif ($c > 239) $bytes = 4;
                elseif ($c > 223) $bytes = 3;
                elseif ($c > 191) $bytes = 2;
                else return false;
                if (($i + $bytes) > $len) return false;
                while ($bytes > 1) {
                    $i++;
                    $b = ord($str[$i]);
                    if ($b < 128 || $b > 191) return false;
                    $bytes--;
                }
            }
        }
        return true;
    }

    /**
     * @name 获取替换变量
     * @author
     * @return array
     */
    private function getParams($goods_info)
    {

        $order_time = date('Y-m-d H:i:s', time());
        if ($this->order->status == 0) {
            $order_time = $this->order->create_time->toDateTimeString();
        }
        if ($this->order->status == 1) {
            $order_time = $this->order->pay_time->toDateTimeString();
        }

        $arr = [
            '[订单编号]' => $this->order->order_sn,
            '[订单金额]' => $this->order->price,
            '[订单时间]' => $order_time,
            '[订单状态]' => $this->order->status_name,
            '[商品编号]' => $goods_info['goods_sn'],
            '[商品条码]' => $goods_info['product_sn'],
            '[优惠金额]' => $this->order->discount_price,
            '[抵扣金额]' => $this->order->deduction_price,
            '[收货地址]' => $this->order->address->address,

            '[运费]' => $this->order->dispatch_price,
            '[姓名]' => $this->order->address->realname,
            '[电话]' => $this->order->address->mobile,
            '[备注]' => $this->order->note
        ];

        if (app('plugins')->isEnabled('package-deliver')){
            if ($this->order->orderDeliver){
                $arr['[收货地址]'] = $this->order->orderDeliver->deliver->full_address;
//                $arr['[自提点名称]'] = $this->order->orderDeliver->deliver->deliver_name;
                $arr['[姓名]'] = $this->order->orderDeliver->deliver->realname;
                $arr['[电话]'] = $this->order->orderDeliver->deliver->deliver_mobile;
            }
        }

        return $arr;


        
//        return [
//            '[订单编号]' => $this->order->order_sn,
//            '[订单金额]' => $this->order->price,
//            '[订单时间]' => $order_time,
//            '[订单状态]' => $this->order->status_name,
//            '[商品编号]' => $goods_info['goods_sn'],
//            '[商品条码]' => $goods_info['product_sn'],
//            '[优惠金额]' => $this->order->discount_price,
//            '[抵扣金额]' => $this->order->deduction_price,
//            '[收货地址]' => $this->order->address->address,
//            '[运费]' => $this->order->dispatch_price,
//            '[姓名]' => $this->order->address->realname,
//            '[电话]' => $this->order->address->mobile,
//            '[备注]' => $this->order->note
//            // '[备注]' => $this->order->hasOneOrderRemark->remark
//        ];
    }

    /**
     * @name 获取打印头
     * @author
     * @return array
     */
    private function getStyle()
    {
        $print_style = explode('|', $this->temp_model->print_style);
        $content = '<CB>' . $this->temp_model->print_title . '</CB><BR>';
        $style = '';
        $len = [];
        foreach ($print_style as $row) {
            $row = explode(':', $row);
            $style .= $this->setSpacing($row[0], $row[1]);
            $len[] = $row[1];
        }
        return array($content, $style, $len);
    }

    /**
     * @name 获取打印内容
     * @author
     * @param $style
     * @param $content
     * @param $len
     * @return string
     */
    private function getContent($style, $content, $len)
    {
        $goods_info = [];
        $content .= $style . '<BR>';
        foreach ($this->order->hasManyOrderGoods as $order_goods) {
            $goods_title = $order_goods->title;
            if ($order_goods->goods_option_title) {
                $goods_title = $order_goods->title . '(' . $order_goods->goods_option_title . ')';
            }
            // 存储商品编号和商品条码，如果需要显示，则需要进行处理
            // 二维数组，goods[0][goods_sn],goods[0][product_sn]，并且进行处理
            $goods_info['title'] = $order_goods->title;
            $goods_info['goods_sn'] .= $goods_title.'编号:'.$order_goods->goods_sn.'<BR>';
            $goods_info['product_sn'] .= $goods_title.'条码:'.$order_goods->product_sn.'<BR>';

            $goods_title_length = (strlen($goods_title) + mb_strlen($goods_title,
                        "UTF8")) / 2;

            if ($goods_title_length > $len[0]) {
                $content .= $goods_title . '<BR>';
                $content .= $this->setSpacing('', $len[0]);
            } else {
                $content .= $this->setSpacing($goods_title, $len[0]);
            }
            $content .= $this->setSpacing($order_goods->goods_price / $order_goods->total,
                $len[1]);
            $content .= $this->setSpacing($order_goods->total, $len[2]);
            $content .= $order_goods->goods_price;
            $content .= '<BR>';
        }
        // 去掉商品编号和商品条码末端的换行，因为后面的操作会加换行，避免重复
        $goods_info['goods_sn'] = rtrim($goods_info['goods_sn'],'<BR>');
        $goods_info['product_sn'] = rtrim($goods_info['product_sn'],'<BR>');

        foreach ($this->temp_model->print_data as $val) {
            $content .= $this->replaceArray($this->getParams($goods_info), $val) . '<BR>';
        }
        \Log::info('temp_content', $content);
        if ($this->temp_model->qr_code) {
            $content .= '<QR>' . $this->temp_model->qr_code . '</QR>';
        }
        return $content;
    }

    /**
     * @name 获取打印数据及参数
     * @author
     * @param $content
     * @return array
     */
    private function getSendMessage($content)
    {
        $self_message = array(
            'user' => $this->printer_model->user,
            'stime' => time(),
            'apiname' => self::API_NAME,
            'debug' => 1,
            'sn' => $this->printer_model->printer_sn,
            'content' => $content,
            'times' => $this->printer_model->times
        );
        $sig = sha1($self_message['user'] . $this->printer_model->ukey . $self_message['stime']);
        $self_message['sig'] = $sig;
        return $self_message;
    }
}