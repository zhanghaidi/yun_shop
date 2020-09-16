<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/6 下午3:08
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Backend\Modules\Sign\Controllers;


use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Yunshop\Sign\Backend\Models\Sign;

class SignController extends BaseController
{
    public function index()
    {
        $records = Sign::records()->withMember();

        $search = \YunShop::request()->search;
        if ($search) {
            $records = $records->search($search)->searchMember($search);
        }

        $page_list = $records->orderBy('updated_at', 'desc')->paginate();
        $page = PaginationHelper::show($page_list->total(), $page_list->currentPage(), $page_list->perPage());
    //dd($page_list);
        return view('Yunshop\Sign::Backend.Sign.sign',[
            'page_list'     => $page_list,
            'page'          => $page,
            'search'        => $search,
            'shopSet'       => \Setting::get('shop.member'),
            'levels'        => MemberLevel::getMemberLevelList(),
            'groups'        => MemberGroup::getMemberGroupList()
        ])->render();
    }


    public function export()
    {
        $sign_name = trans('Yunshop\Sign::sign.plugin_name');

        $file_name = date('Ymdhis', time()) . $sign_name . '明细导出';

        $search = \YunShop::request()->search;
        $builder = Sign::records()->search($search)->searchMember($search);

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);

        $export_data[0] = ['会员ID', '会员姓名', '会员手机号', '最新' . $sign_name . '时间', '今日' . $sign_name . '状态', '连续' . $sign_name . '状态', '累计奖励积分', '累计奖励优惠券（张）'];

        foreach ($export_model->builder_model as $key => $item) {
            $export_data[$key + 1] = [
                $item->member_id,
                $item->member->realname ?: $item->member->nickname,
                $item->mobile ?: '',
                $item->updated_at->toDateTimeString(),
                $item->sign_status ? "已" . $sign_name : "未" . $sign_name,
                $item->cumulative_name,
                $item->cumulative_point,
                $item->cumulative_coupon,
            ];
        }

        $export_model->export($file_name, $export_data, \Request::query('route'));
    }

}
