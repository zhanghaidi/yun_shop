<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 9:43
 */

namespace Yunshop\Supplier\frontend;


use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Supplier\supplier\models\SupplierWithdraw;

class WithdrawLogController extends ApiController
{
    //全部
    public function index()
    {
        $member_id = \YunShop::app()->getMemberId();
        $params = \YunShop::request();
        $list = SupplierWithdraw::getWithdrawList($params)->where('member_id', $member_id)->paginate(20)->toArray();

        return $this->successJson('ok', $list);
    }

    //申请中
    public function applying()
    {
        $member_id = \YunShop::app()->getMemberId();
        $params = \YunShop::request();
        $list = SupplierWithdraw::getWithdrawList($params, 1)->where('member_id', $member_id)->paginate(20)->toArray();

        return $this->successJson('ok', $list);
    }

    //待打款
    public function pending()
    {
        $member_id = \YunShop::app()->getMemberId();
        $params = \YunShop::request();
        $list = SupplierWithdraw::getWithdrawList($params, 2)->where('member_id', $member_id)->paginate(20)->toArray();

        return $this->successJson('ok', $list);
    }

    //已打款
    public function already()
    {
        $member_id = \YunShop::app()->getMemberId();
        $params = \YunShop::request();
        $list = SupplierWithdraw::getWithdrawList($params, 3)->where('member_id', $member_id)->paginate(20)->toArray();

        return $this->successJson('ok', $list);
    }

    //驳回
    public function reject()
    {
        $member_id = \YunShop::app()->getMemberId();
        $params = \YunShop::request();
        $list = SupplierWithdraw::getWithdrawList($params, -1)->where('member_id', $member_id)->paginate(20)->toArray();

        return $this->successJson('ok', $list);
    }
}