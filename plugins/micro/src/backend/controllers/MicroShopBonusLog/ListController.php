<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/15
 * Time: 下午3:02
 */

namespace Yunshop\Micro\backend\controllers\MicroShopBonusLog;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Micro\common\models\MicroShopBonusLog;
use Yunshop\Micro\common\models\MicroShopLevel;

class ListController extends BaseController
{
    private $columns = ['下单时间', '支付时间', '完成时间', '订单编号', '店主', '店主等级', '业务类型', '商品金额', '分红结算金额', '下级店主分红金额', '分红比例', '分红状态'];

    public function index()
    {
        $search = \YunShop::request()->search;
        $list = MicroShopBonusLog::getBonusLogList($search)->orderBy('id', 'desc')->paginate(10);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        $levels = MicroShopLevel::getLevelList()->get();

        //todo 未结算分红
        $no_bonus_money = MicroShopBonusLog::getBonusLogList(null, 0)->get()->sum('bonus_money');
        //todo 已结算分红
        $ok_bonus_money = MicroShopBonusLog::getBonusLogList(null, 1)->get()->sum('bonus_money');
        //todo 分红总金额
        $total_bonus_money = MicroShopBonusLog::getBonusLogList(null)->get()->sum('bonus_money');

        return view('Yunshop\Micro::backend.MicroShopBonusLog.list', [
            'list'              => $list,
            'request'           => $search,
            'member_detail_url' => 'member.member.detail',
            'levels'            => $levels,
            'no_bonus_money'    => $no_bonus_money,
            'ok_bonus_money'    => $ok_bonus_money,
            'total_bonus_money' => $total_bonus_money,
            'pager'             => $pager
        ]);
    }

    public function export()
    {
        $file_name = date('Ymdhis', time()) . '分红记录导出';
        $search = \YunShop::request()->search;
        $list = MicroShopBonusLog::getBonusLogList($search)->get();
        $export_data[0] = $this->columns;

        foreach ($list as $key => $item) {
            $export_data[$key + 1] = [
                $item->created_at,
                date('Y-m-d H:i:s', $item->pay_time),
                date('Y-m-d H:i:s', $item->complete_time),
                $item->order_sn,
                $item->hasOneMember->nickname,
                $item->hasOneMicroShopLevel->level_name,
                $item->mode_type,
                $item->goods_price,
                $item->bonus_money,
                $item->lower_level_bonus_money,
                $item->bonus_ratio,
                $item->status_name
            ];
        }

        \Excel::create($file_name, function ($excel) use ($export_data) {
            $excel->setTitle('Office 2005 XLSX Document');
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");
            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });
        })->export('xls');
    }
}