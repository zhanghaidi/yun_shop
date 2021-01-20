<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/3/6
 * Time: 11:41 PM
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Mryt\common\models\Log;

class LogController extends BaseController
{
    public function index()
    {
        $search = request()->search;
        $list = Log::getList($search)->orderBy('id', 'desc')->paginate();
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Mryt::admin.log', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search
        ])->render();
    }
}