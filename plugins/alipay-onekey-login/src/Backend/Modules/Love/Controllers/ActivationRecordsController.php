<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/28 下午2:04
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Love\Controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Love\Backend\Modules\Love\Models\LoveActivationRecords;

class ActivationRecordsController extends BaseController
{
    const PAGE_SIZE = 10;

    public function index()
    {
        $records = LoveActivationRecords::records();
//dd(\YunShop::request());
        //dd(\YunShop::request()->search);
        $search = $this->getPostSearch();
        //dd($search);
        if ($search) {
            //dd($search);
            // 搜索功能
            $records = $records->search($search)->searchMember($search);
        }

        $pageList = $records->orderBy('created_at','desc')->paginate(static::PAGE_SIZE);
        $page = PaginationHelper::show($pageList->total(),$pageList->currentPage(),$pageList->perPage());

        return view('Yunshop\Love::Backend.Love.activationRecords',[
            'pageList'      => $pageList,
            'page'          => $page,
            'shopSet'       => \Setting::get('shop.member'),
            'search'        => $search,

        ])->render();
    }

    private function getPostSearch()
    {
        return \YunShop::request()->search;
    }



}
