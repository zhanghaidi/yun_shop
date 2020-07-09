<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/5
 * Time: 15:00
 */

namespace Yunshop\Supplier\common\services\order;

use app\common\components\BaseController;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\Order;
use app\common\exceptions\AppException;
use Illuminate\Support\Facades\DB;
use app\frontend\modules\order\services\OrderService;

use Yunshop\Supplier\common\order\OrderSend;
use Yunshop\FddContract\models\ContractSign;
use Yunshop\FddContract\models\Personal;
use Yunshop\FddContract\services\FDDService;

class OperationController extends BaseController
{
    protected $param;
    protected $order;

    public function __construct()
    {
        parent::__construct();
        $this->param = request()->input();
        if (!isset($this->param['order_id'])) {
            return $this->message('order_id不能为空!', '', 'error');

        }
        $this->order = Order::find($this->param['order_id']);
        if (!isset($this->order)) {
            return $this->message('未找到该订单!', '', 'error');

        }
    }
    //确认接单
    public function acceptReceipt()
    {
        $order = $this->order;

        //$order->currentProcess()->toNextStatus();

        return $this->successJson('成功');
    }

    public function driverSend()
    {
        $orderOperation = OrderSend::find($this->param['order_id']);

        if (!isset($orderOperation)) {
            throw new AppException('未找到该订单');
        }
        DB::transaction(function() use($orderOperation) {
            $orderOperation->handle();
        });

        return $this->message('操作成功');
    }

    public function contractSign()
    {
        $fdd = new FDDService();
        $store_id = \YunShop::request()->store_id;
        $uid = Store::getStoreById($store_id)->first()->uid;
        $personal = Personal::getCustomerByUid($uid)->first();
        $order_sn = \YunShop::request()->order_sn;
        $data = ContractSign::where('order_sn', $order_sn)->first();
        if (!$data) {
            die(json_encode(array(
                "result" => 0,
                "status" => -999,
                "msg" => "该订单无合同"
            )));
        }
        if ($data->client_download_url) {
            die(json_encode(array(
                "result" => 1,
                "status" => $data->status,
                "msg" => "该订单已签署合同"
            )));
        }

        if ($data->shop_download_url) {
            $transaction_id = 'CRS'.date('YmdHis') . Client::random(4, false);
            $notifyUrl = Url::shopSchemeUrl('payment/contract/storeNotifyUrl.php');
            $returnUrl = Url::shopSchemeUrl('payment/contract/storeReturnUrl.php');
            $data->client_transaction_id = $transaction_id;
            $data->save();
            $get_url = $fdd->extsign($personal->customer_id, $transaction_id, $data->contract_id, $data->contract_title,$returnUrl, $notifyUrl);

            if ($get_url) {
                die(json_encode(array(
                    "result" => 0,
                    "status" => $data->status,
                    "get_url" => $get_url,
                    "msg" => "该订单未签署合同"
                )));
            }
        }
        die(json_encode(array(
            "result" => 0,
            "status" => $data->status,
            "msg" => "合同签署失败，请重新下单"
        )));
    }

    public function realName()
    {
        $store_id = \YunShop::request()->store_id;
        $uid = Store::getStoreById($store_id)->first()->uid;
        $personal = Personal::getCustomerByUid($uid)->first();
        if ($personal['customer_id']) {
            die(json_encode(array(
                "result" => 1,
                "store_id" => $store_id,
                "msg" => "获取实名信息成功"
            )));
        }
        die(json_encode(array(
            "result" => 0,
            "store_id" => $store_id,
            "msg" => "未实名"
        )));
    }
}