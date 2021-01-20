<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/23
 * Time: 下午5:20
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Mryt\models\MrytLevelModel;
use Yunshop\Mryt\models\MrytMemberAddUpVipModel;

class TeamnewController extends BaseController
{
    public function index()
    {
        $result = [];
        $search = \YunShop::request()->search;

        $search_month = [
            date('Ym', strtotime('-1 month')),
            date('Ym', strtotime('-2 month')),
            date('Ym', strtotime('-3 month'))
        ];

        $search['search_month'] = $search_month;

        $show_month_1 = date('Ym', strtotime('-1 month'));
        $show_month_2 = date('Ym', strtotime('-2 month'));
        $show_month_3 = date('Ym', strtotime('-3 month'));

        $list =MrytMemberAddUpVipModel::getList($search)->get();

        if (!is_null($list)) {
            foreach ($list as $item) {
                if (empty($result[$item->uid])) {
                    $result[$item->uid] = [
                        'nickname' => $item->hasOneMcMember->nickname,
                        'avatar'   => $item->hasOneMcMember->avatar,
                        'level'    => $item->hasOneMember->hasOneLevel->level_name,
                        $show_month_1 => 0,
                        $show_month_2 => 0,
                        $show_month_3 => 0
                    ];
                }

                $result[$item->uid][$item->curr_date] = $item->nums;
            }
        }

        $level = MrytLevelModel::getList()->get();


       // $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('Yunshop\Mryt::admin.teamnew', [
            'list' => $result,
            'show_month_1' => $show_month_1,
            'show_month_2' => $show_month_2,
            'show_month_3' => $show_month_3,
            'level'        => $level,
            'search'       => $search
        ])->render();
    }
}