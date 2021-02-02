<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use app\frontend\models\Order;

//发票申请控制器
class InvoiceController extends ApiController
{
    protected $user_id = 0;
    protected $uniacid = 0;

    /**
     *  constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->uniacid = \YunShop::app()->uniacid;
        $this->user_id = \YunShop::app()->getMemberId();
    }

    /**
     * 发票申请接口
     */
    public function invoiceApply()
    {
        $input_data = request()->all();
        $map = ['id' => $input_data['order_id'], 'uniacid' => $this->uniacid]; // 必填参数检测
        if (!array_key_exists('order_id', $input_data) || !$input_data['order_id']) {
            return $this->errorJson('缺少参数【订单ID】');
        }
        $data = ['invoice_status' => 1];
        $required = ['invoice_type' => '发票类型', 'rise_type' => '抬头类型', 'invoice_send_to_email' => '用户邮箱'];
        foreach ($required as $k => $v) {
            if (!array_key_exists($k, $input_data)) {
                return $this->errorJson('缺少参数【' . $v . '】');
            }
            $data[$k] = $input_data[$k];
        }
        if ($input_data['invoice_type'] == 1) { // 限制只能开具电子发票
            return $this->errorJson('暂不支持纸质发票');
        }
        $order_info = Order::where($map)->first();//获取订单信息
        if (!$order_info || $order_info['status'] == 2) {
            return $this->errorJson('不可修改');
        }
        if ($input_data['rise_type'] == 0) { // 抬头类型：单位
            $required = ['company_name' => '单位名称', 'tax_number' => '纳税人识别号'];
            foreach ($required as $k => $v) {
                if (!array_key_exists($k, $input_data)) {
                    return $this->errorJson('参数缺失【' . $v . '】');
                }
                $data[$k] = $input_data[$k];
            }
        } elseif ($input_data['rise_type'] == 1) { // 抬头类型：个人
            if (!array_key_exists('rise_text', $input_data)) {
                return $this->errorJson('缺少参数【发票抬头】');
            }
            $data['rise_text'] = $input_data['rise_text'];
        }

        $result = Order::where($map)->update($data);
        if (!$result) {
            return $this->errorJson('提交出错了');
        }

        return $this->successJson('提交成功', $result);
    }

    /**
     *查看发票信息
     */
    public function getInvoiceInfo()
    {
        $input_data = request()->all();
        $map = [];
        if (array_key_exists('order_id', $input_data)) {
            $map['id'] = $input_data['order_id'];
            $map['uid'] = $this->user_id;
        }
        if (array_key_exists('rise_type', $input_data)) {
            $map = [
                'invoice_status >' => 0,
                'rise_type' => $input_data['rise_type'],
                'uniacid' => $this->uniacid,
                'uid' => $this->user_id,
            ];
        }
        if (empty($map)) {
            return $this->errorJson('缺少查询参数【订单ID或抬头类型】');
        }
        $field = ['invoice_status', 'invoice_error', 'invoice_type', 'invoice', 'rise_type', 'rise_text', 'company_name', 'tax_number', 'invoice_send_to_email'];
        $invoice_info = Order::where($map)->orderBy('id', 'DESC')->select($field)->first();//获取订单信息

        return $this->successJson('获取成功', $invoice_info);
    }
}
