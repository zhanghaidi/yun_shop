<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/28 下午2:03
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Love\Controllers;

use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Yunshop\Love\Backend\Modules\Love\Models\LoveRecords;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Common\Services\ConstService;

class ChangeRecordsController extends BaseController
{
    const PAGE_SIZE = 15;

    /**
     * 爱心值变动明细接口
     * @return string
     */
    public function index()
    {
        $records = LoveRecords::records();

        $search = $this->getPostSearch();
        if ($search) {
            $records->search($search)->searchMember($search);
        }

        $pageList = $records->orderBy('created_at', 'desc')->paginate(static::PAGE_SIZE);
        $page = PaginationHelper::show($pageList->total(), $pageList->currentPage(), $pageList->perPage());

        return view('Yunshop\Love::Backend.Love.changeRecords',[
            'pageList'      => $pageList,
            'page'          => $page,
            'search'        => $search,
            'shopSet'       => \Setting::get('shop.member'),
            'memberLevels'  => MemberLevel::getMemberLevelList(),
            'memberGroups'  => MemberGroup::getMemberGroupList(),
            'sourceName'    => $this->getSourceComment()
        ])->render();
    }

    /**
     * 爱心值变动明细导出
     */
    public function export()
    {
        $love_name = $this->getLoveName();
        $file_name = date('Ymdhis', time()) . $love_name . '明细导出';

        $search = $this->getPostSearch();
        $builder = LoveRecords::records()->search($search)->searchMember($search);

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);

        $export_data[0] = ['时间', '会员ID', '会员姓名', '会员手机号', '会员等级', '会员分组', '订单号', '业务类型', '收入／支出','变动前' . $love_name,'变动' . $love_name, '变动后剩余' . $love_name,'可用／冻结'];

        $shopSet = $this->getShopSet();


        foreach ($export_model->builder_model as $key => $item) {

            if ($item->member) {

                $member_id          = $item->member->uid;
                $member_name        = $item->member->realname ?: $item->member->nickname;
                $member_mobile      = $item->member->mobile;
                $member_level       = $shopSet['level_name'];
                $member_group       = '无分组';

                if ($item->member->yzMember->group) {
                    $member_group       = $item->member->yzMember->group->group_name ?: '无分组';
                }
                if ($item->member->yzMember->level) {
                    $member_level       = $item->member->yzMember->level->level_name ?: $shopSet['level_name'];
                }


            } else {
                $member_id          = '';
                $member_name        = '';
                $member_mobile      = '';
                $member_level       = $shopSet['level_name'];
                $member_group       = '无分组';
            }

            $export_data[$key + 1] = [
                $item->created_at,
                $member_id,
                $member_name,
                $member_mobile,
                $member_level,
                $member_group,
                $item->relation,
                $item->source_name,
                $item->type_name,
                $item->old_value,
                $item->change_value,
                $item->new_value,
                $item->value_type_name
            ];
        }
        $export_model->export($file_name, $export_data, \Request::query('route'));
    }

    private function getSourceComment()
    {
        return (new ConstService($this->getLoveName()))->sourceComment();
    }

    private function getPostSearch()
    {
        return \YunShop::request()->search;
    }

    /**
     * 获取插件名称
     * @return mixed|string
     */
    private function getLoveName()
    {
        return CommonService::getLoveName();
    }

    /**
     * 获取商城会员基础设置
     * @return mixed
     */
    private function getShopSet()
    {
        return \Setting::get('shop.member');
    }


}