<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/17
 * Time: 下午4:31
 */

namespace Yunshop\Micro\frontend\controllers\MicroShopBonusLog;


use app\common\components\ApiController;
use Yunshop\Micro\common\models\MicroShopBonusLog;

class DetailController extends ApiController
{
    public function index()
    {
        $log_id = \YunShop::request()->log_id;
        $log = MicroShopBonusLog::getBonusLogByLogId($log_id);
        if ($log->is_lower == 0) {
            $data = $this->getData($log);
        } else {
            $data = $this->getLowerData($log);
        }
        return $this->successJson('成功', [
            'status'    => 1,
            'log'       => $data
        ]);
    }

    public function getLowerData($log)
    {
        $data = [
            'module1' => [
                'title'  => $log->mode_type,
                'money'  => $log->is_lower == 0 ? $log->bonus_money : $log->lower_level_bonus_money,
                'status' => $log->status_name
            ],
            'module2' => [
                [
                    'name' => trans('Yunshop\Micro::pack.lower_micro_shop'),
                    'value' => $log->lower_level_nickname
                ],
                [
                    'name' => trans('Yunshop\Micro::pack.lower_micro_shop_bonus_money'),
                    'value' => $log->lower_level_bonus_money . '元'
                ],
                [
                    'name' => trans('Yunshop\Micro::pack.agent_micro_shop_bonus_ratio'),
                    'value' => $log->agent_bonus_ratio . '%'
                ]
            ],
            'module3' => [
                [
                    'name' => '订单状态',
                    'value' => $log->order_status_name
                ],
                [
                    'name' => '订单编号',
                    'value' => $log->order_sn
                ],
                /*[
                    'name' => '支付编号',
                    'value' => $log->pay_sn
                ],*/
                [
                    'name' => '下单时间',
                    'value' => $log->created_at->toDateTimeString()
                ],
                /*[
                    'name' => '付款时间',
                    'value' => $log->pay_time->toDateTimeString()
                ],*/
                /*[
                    'name' => '完成时间',
                    'value' => $log->complete_time->toDateTimeString()
                ],*/
                /*[
                    'name' => '失效时间',
                    'value' => $log->refund_time->toDateTimeString()
                ],*/
            ]
        ];
        if ($log->order_status == 1) {
            $data['module3'][] = [
                'name' => '支付编号',
                'value' => $log->pay_sn
            ];
            $data['module3'][] = [
                'name' => '付款时间',
                'value' => $log->pay_time->toDateTimeString()
            ];
        }
        if ($log->order_status == 3) {
            $data['module3'][] = [
                'name' => '完成时间',
                'value' => $log->complete_time->toDateTimeString()
            ];
        }
        if ($log->order_status == -1) {
            $data['module3'][] = [
                'name' => '失效时间',
                'value' => $log->refund_time->toDateTimeString()
            ];
        }
        return $data;
    }

    public function getData($log)
    {
        $data = [
            'module1' => [
                'title'  => $log->mode_type,
                'money'  => $log->is_lower == 0 ? $log->bonus_money : $log->lower_level_bonus_money,
                'status' => $log->status_name
            ],
            'module2' => [
                [
                    'name'  => '购买会员',
                    'value' =>  $log->order_buyer
                ],
                [
                    'name'  => '商品金额',
                    'value' =>  $log->goods_price . '元'
                ],
                [
                    'name'  => '商品名',
                    'value' =>  $log->goods_title
                ],
                [
                    'name'  => trans('Yunshop\Micro::pack.bonus_ratio'),
                    'value' =>  $log->bonus_ratio . '%'
                ],
                /*[
                    'name'  => '支付方式',
                    'value' =>  $log->pay_type
                ]*/
            ],
            'module3' => [
                [
                    'name'  => '订单状态',
                    'value' =>  $log->order_status_name
                ],
                [
                    'name'  => '订单编号',
                    'value' =>  $log->order_sn
                ],
                /*[
                    'name'  => '支付编号',
                    'value' =>  $log->pay_sn
                ],*/
                [
                    'name'  => '下单时间',
                    'value' =>  $log->created_at->toDateTimeString()
                ],
                /*[
                    'name'  => '支付时间',
                    'value' =>  $log->pay_time
                ],*/
                /*[
                    'name'  => '完成时间',
                    'value' =>  $log->complete_time
                ],
                [
                    'name'  => '结算时间',
                    'value' =>  $log->apply_time
                ]*/
            ]
        ];

        if (!empty($log->pay_type)) {
            $data['module2'][] = [
                'name'  => '支付方式',
                'value' =>  $log->pay_type
            ];
        }
        if (!empty($log->pay_sn)) {
            $data['module3'][] = [
                'name'  => '支付编号',
                'value' =>  $log->pay_sn
            ];
        }
        if ($log->order_status == 1) {
            $data['module3'][] = [
                'name'  => '支付时间',
                'value' =>  $log->pay_time->toDateTimeString()
            ];
        }
        if ($log->complete_time == 3) {
            $data['module3'][]= [
                'name'  => '完成时间',
                'value' =>  $log->complete_time->toDateTimeString()
            ];
        }
        if ($log->apply_status == 1) {
            $data['module3'][] = [
                'name'  => '结算时间',
                'value' =>  date('Y-m-d H:i', $log->apply_time)
            ];
        }
        return $data;
    }
}