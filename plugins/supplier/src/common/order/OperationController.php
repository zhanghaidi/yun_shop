<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/17
 * Time: 11:24
 */

namespace Yunshop\Supplier\common\order;

use app\common\components\BaseController;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\Order;
use app\common\exceptions\AppException;
use Illuminate\Support\Facades\DB;
use app\frontend\modules\order\services\OrderService;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\order\OrderSend;


class OperationController extends BaseController
{
    protected $param;
    protected $order;

    public function __construct()
    {
        parent::__construct();
        $this->param = \Request::input();
        if (!isset($this->param['order_id'])) {
            return $this->message('order_id不能为空!', '', 'error');

        }
        $this->order = Order::find($this->param['order_id']);
        if (!isset($this->order)) {
            return $this->message('未找到该订单!', '', 'error');

        }
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function send()
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

}