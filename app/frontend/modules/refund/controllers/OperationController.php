<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/13
 * Time: 下午2:17
 */

namespace app\frontend\modules\refund\controllers;


use app\common\components\ApiController;
use app\common\exceptions\AdminException;
use app\common\modules\refund\services\RefundService;
use app\frontend\modules\refund\models\RefundApply;
use app\frontend\modules\refund\services\RefundMessageService;
use app\frontend\modules\refund\services\RefundOperationService;
use Illuminate\Support\Facades\DB;
use app\frontend\modules\order\services\OrderService;
use app\common\facades\Setting;

class OperationController extends ApiController
{
    public $transactionActions = ['*'];

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function send()
    {
        $request = request()->input();
        $this->validate([
            'refund_id' => 'required|filled|integer',
            'express_company_code' => 'required|string',
            'express_company_name' => 'required|string',
            'express_sn' => 'required|filled|string',
        ]);
        RefundOperationService::refundSend();

        if (!empty($request['refund_id'])) {
            $jushuitanSetRs = Setting::get('shop.order');
            $jushuitanSetRs = array_filter($jushuitanSetRs);
            if (!isset($jushuitanSetRs['jushuitan_shop_id'])){
                return $this->errorJson('配置错误');
            }

            $data = Db::table('yz_order_refund')->where(['id' => $request['refund_id']])->first();
            $order_data = Db::table('yz_order')->where(['id' => $data['order_id']])->first();
            $array[] =
                [
                    'sku_id' => 'TP0024',
                    'qty' => floatval($order_data['goods_total']),
                    'amount' => floatval($data['price']),
                    'type' => '退货',
                ];
            $params = array(
                [
                    "shop_id" => (int) $jushuitanSetRs['jushuitan_shop_id'],
                    "outer_as_id" => $data['refund_sn'],
                    "so_id" => $order_data['order_sn'],
                    "type" => '普通退货',
                    "logistics_company" => $request['express_company_name'],
                    "l_id" => $request['express_sn'],
                    "shop_status" => 'WAIT_SELLER_CONFIRM_GOODS',
                    "remark" => $data['content'],
                    "good_status" => 'BUYER_RETURNED_GOODS',
                    "question_type" => '买家退款，退货',
                    "total_amount" => floatval($data['price']),
                    "refund" => floatval($data['price']),
                    "payment" => floatval(0),
                    "iteams" => $array,

                ]
            );

            $result = OrderService::post($params, 'aftersale.upload');
            if (!empty($result) && $result['code'] == 0) {
                return $this->successJson();
            } else {
                return $this->errorJson('物流添加失败，请联系管理员');
            }
        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function complete()
    {
        $this->validate([
            'refund_id' => 'required|filled|integer',
        ]);

        RefundOperationService::refundComplete();

        return $this->successJson();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function cancel()
    {
        $this->validate([
            'refund_id' => 'required|filled|integer',
        ]);
        RefundOperationService::refundCancel();
        return $this->successJson();

    }

}