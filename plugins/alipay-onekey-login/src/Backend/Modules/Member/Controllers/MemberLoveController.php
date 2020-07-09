<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/28 下午2:15
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Member\Controllers;


use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Love\Backend\Modules\Love\Models\MemberLove;
use Yunshop\Love\Common\Models\Member;

class MemberLoveController extends BaseController
{
    const PAGE_SIZE = 15;


    public function index()
    {

        $memberLove = new MemberLove();

        $usable = $memberLove->sum('usable');


        $froze = $memberLove->sum('froze');

        //dd($froze);

        $records = Member::records()->withLove();
//        var_dump($records);die;
        $search = $this->getPostSearch();
        if ($search) {
            $records->search($search)->searchLove($search);
        }
//        var_dump($records);die;
        $pageList = $records->orderBy('createtime', 'desc')->paginate(static::PAGE_SIZE);

        $page = PaginationHelper::show($pageList->total(), $pageList->currentPage(), $pageList->perPage());

        return view('Yunshop\Love::Backend.Member.memberLove',[
            'pageList'      => $pageList,
            'page'          => $page,
            'search'        => $search,
            'usable'        => $usable,
            'froze'         => $froze,
            'shopSet'       => \Setting::get('shop.member'),
            'memberLevels'   => MemberLevel::getMemberLevelList(),
            'memberGroups'   => MemberGroup::getMemberGroupList()
        ])->render();
    }


    private function getPostSearch()
    {
        return \YunShop::request()->search;
    }

}
