<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/28
 * Time: 13:59
 */

namespace Yunshop\FxActivity\models;

use app\backend\modules\order\services\OrderService;
use Illuminate\Database\Eloquent\Builder;
use app\common\helpers\QrCodeHelper;
use app\common\helpers\Url;
use app\common\models\order\Address as OrderAddress;
use app\common\models\order\Express;
use app\common\models\order\OrderChangePriceLog;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;
use app\common\models\order\OrderDiscount;
use app\common\models\order\OrderSetting;
use app\common\models\order\Pay;
use app\common\models\order\Plugin;
use app\common\models\order\Remark;
use app\common\models\refund\RefundApply;
use app\frontend\modules\order\services\status\StatusFactory;
use Illuminate\Support\Facades\DB;
use app\backend\modules\order\observers\OrderObserver;
use Illuminate\Database\Eloquent\Model;

class OrderGoods extends \app\frontend\modules\order\models\PreOrder
{
    public $table = 'yz_order_goods';
}