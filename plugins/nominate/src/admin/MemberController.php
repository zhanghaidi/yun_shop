<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/16
 * Time: 2:42 PM
 */

namespace Yunshop\Nominate\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Yunshop\Nominate\models\ShopMemberBackend;
use Yunshop\Nominate\models\ShopMemberLevel;

class MemberController extends BaseController
{
    private function getMemberLevelList()
    {
        return ShopMemberLevel::select(['id', 'level', 'level_name'])
            ->orderBy('level', 'desc')
            ->get();
    }

    public function index()
    {
        $search = request()->search;
        $list = ShopMemberBackend::getList($search)->orderBy('member_id', 'desc')->paginate();
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('Yunshop\Nominate::member.index', [
            'list'      => $list,
            'pager'     => $pager,
            'levels'    => $this->getMemberLevelList(),
            'set'       => \Setting::get('plugin.nominate')
        ])->render();
    }

    public function export()
    {
        $set = \Setting::get('plugin.store');
        $search = request()->search;
        $builder = ShopMemberBackend::getList($search);
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '推荐-会员导出';

        $nominatePrizeName = $set['nominate_prize_name'] ?: '直推奖';
        $nominatePoorPrizeName = $set['nominate_poor_prize_name'] ?: '直推极差奖';
        $teamPrizeName = $set['team_prize_name'] ?: '团队奖';
        $teamManagePrizeName = $set['team_manage_prize_name'] ?: '团队业绩奖';

        $export_data[0] = ['会员ID', '推荐人', '会员信息', '等级名称', '累计'.$nominatePrizeName, '累计'.$nominatePoorPrizeName, '累计'.$teamPrizeName, '累计'.$teamManagePrizeName, '累计总奖励'];

        foreach ($export_model->builder_model as $key => $item) {
            $parent = $item->parent ? $item->parent->nickname : '总店';
            $member = $item->hasOneMember ? $item->hasOneMember->nickname : '未更新';
            $export_data[$key + 1] = [
                $item->member_id,
                $parent,
                $member,
                $item->shopMemberLevel->level_name,
                $item['nominate_prize_amount'],
                $item['nominate_poor_prize_amount'],
                $item['team_prize_amount'],
                $item['team_manage_prize_amount'],
                $item['team_manage_prize_amount'] + $item['team_prize_amount'] + $item['nominate_poor_prize_amount'] + $item['nominate_prize_amount']
            ];
        }
        $export_model->export($file_name, $export_data, \Request::query('route'));
    }
}