<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/21
 * Time: 下午6:16
 */

namespace app\frontend\modules\refund\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\models\Order;
use app\common\modules\refund\services\RefundService;
use app\backend\modules\refund\services\RefundMessageService;
use app\backend\modules\refund\models\RefundApply;
use Illuminate\Support\Facades\DB;
use app\frontend\modules\order\services\OrderService;



class PayController extends ApiController
{
    private $refundApply;   
    public $transactionActions = [];

    public function preAction()
    {
        parent::preAction();
    }

    /**
     * {@inheritdoc}
     */
    public function index($refund_id)
    {

        $this->refundApply = RefundApply::find($refund_id);
        if (!isset($this->refundApply)) {
            throw new AppException('退款记录不存在');
        }
        /**
         * @var $this ->refundApply RefundApply
         */
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($refund_id) {
            $result = (new RefundService)->pay($refund_id);
            if (!$result) {
                throw new AppException('自动审核操作失败');
            }

            return $result;
        });

        if (is_string($result)) {
            redirect($result)->send();
        }

        if (is_array($result) && isset($result['action']) && isset($result['input'])) {
           echo $this->formPost($result);exit();
        }

        RefundMessageService::passMessage($this->refundApply);//通知买家

        return true;

    }

    /**
     * 表单POST请求
     * @param $trxCode
     * @param $data
     */
    public function formPost($data)
    {

        $echo = "<form style='display:none;' id='form1' name='form1' method='post' action='" . $data['action']."'>";
        foreach ($data['input'] as $k => $v) {
            $echo .= "<input name='{$k}' type='text' value='{$v}' />";
        }
        $echo .= "</form>";
        $echo .= "<script type='text/javascript'>function load_submit(){document.form1.submit()}load_submit();</script>";

        echo $echo;
    }

}