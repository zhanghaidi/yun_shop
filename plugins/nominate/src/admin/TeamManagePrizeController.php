<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/23
 * Time: 7:33 PM
 */

namespace Yunshop\Nominate\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Nominate\models\TeamPrize;
use app\common\services\ExportService;
use app\common\exceptions\ShopException;

class TeamManagePrizeController extends BaseController
{
    public function index()
    {
        $search = request()->search;

        $list = TeamPrize::getList($search)
            ->orderBy('id', 'desc')
            ->paginate();
        $amountTotal = TeamPrize::getList($search)
            ->sum('amount');
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        $set = \Setting::get('plugin.nominate');

        return view('Yunshop\Nominate::team-manage-prize.index', [
            'list' => $list,
            'pager' => $pager,
            'amountTotal' => $amountTotal,
            'set' => $set,
            'search' => $search,
        ])->render();
    }

    public function export()
    {
        $search = request()->search;
        $builder = TeamPrize::getList($search)->orderBy('id', 'desc');
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '推荐-团队业绩奖导出';

        if ($export_model->builder_model->isEmpty()) {
            throw new ShopException('导出数据为空');
        }

        $export_data[0] = ['ID', '时间', '会员ID', '会员昵称', '会员姓名', '会员手机号','会员等级',
                           '订单编号', '订单金额',
                            '分红比例', '奖励金额', '奖励发放状态'];

        foreach ($export_model->builder_model as $key => $item) {

            $export_data[$key + 1] = [
                $item['id'],
                $item['created_at'],
                $item['member']['uid'],  //会员ID
                $item['member']['nickname'],  //会员昵称
                $item['member']['realname'],  //会员姓名
                $item['member']['mobile'],
                $item['memberLevel']['level_name'], //会员等级
                
                $item['order']['order_sn'],   //订单编号
                $item['order']['price'],

                $item['ratio'],    //分红比例
                $item['amount'],    //奖励金额
                $item['status_name'],    //奖励发放状态
            ];
        }
        $export_model->export($file_name, $export_data, \Request::query('route'));
    }
}