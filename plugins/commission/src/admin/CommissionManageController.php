<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/5/21
 * Time: 下午1:18
 */

namespace Yunshop\Commission\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Commission\models\CommissionManage;

class CommissionManageController extends BaseController
{
    public function index()
    {

        $search = \YunShop::request()->get('search');
        $pageSize = 10;
        $list = CommissionManage::getManages($search)->orderBy('id','desc')->paginate($pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('Yunshop\Commission::admin.manage-list', [
            'total' => $list->total(),
            'list' => $list,
            'search' => $search,
            'pager' => $pager,
        ])->render();
    }
}