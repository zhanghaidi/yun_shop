<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/12
 * Time: 上午11:07
 */

namespace Yunshop\Micro\backend\controllers\MicroShop;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopBonusLog;
use Yunshop\Micro\common\models\MicroShopLevel;
use Yunshop\Micro\common\services\MicroShop\MicroShopService;

class ListController extends BaseController
{
    private $columns = ['开店时间', '微店名称', '店主', '店主等级', '下级微店店主总数	', '微店消费总金额', '已结算分红', '累计分红'];

    private $member_detail_url = 'member.member.detail';

    private function getMap($micro_list)
    {
        $micro_list->map(function ($micro){
            // todo 下级微店人数
            $micro->lower_total = MicroShopService::getLowerTotal($micro->member_id);
            // todo 微店消费总金额
            $micro->money_total = MicroShopBonusLog::getBonusLogByMemberId($micro->member_id)->sum('goods_price');
            // todo 已结算分红
            $micro->ok_total    = MicroShopBonusLog::getBonusLogByMemberId($micro->member_id)->applyStatus(1)->sum('bonus_money');
            // todo 累计分红
            $micro->sum_total   = MicroShopBonusLog::getBonusLogByMemberId($micro->member_id)->sum('bonus_money');
        });
        return $micro_list;
    }

    public function index()
    {
        $search = \YunShop::request()->search;
        $micro_list = MicroShop::getMicroShopList($search)->orderBy('id', 'desc')->paginate(10);
        $micro_list = $this->getMap($micro_list);
        $pager = PaginationHelper::show($micro_list->total(), $micro_list->currentPage(), $micro_list->perPage());

        $levels = MicroShopLevel::getLevelList()->get();

        return view('Yunshop\Micro::backend.MicroShop.list', [
            'list'              => $micro_list,
            'levels'            => $levels,
            'request'           => $search,
            'member_detail_url' => $this->member_detail_url,
            'pager'             => $pager
        ])->render();
    }

    public function export()
    {
        $file_name = date('Ymdhis', time()) . '微店导出';
        $search = \YunShop::request()->search;
        $micro_list = MicroShop::getMicroShopList($search)->get();
        $micro_list = $this->getMap($micro_list);
        $export_data[0] = $this->columns;

        foreach ($micro_list as $key => $item) {
            $export_data[$key + 1] = [
                $item->created_at,
                $item->shop_name,
                $item->hasOneMember->nickname,
                $item->hasOneMicroShopLevel->level_name,
                $item->lower_total,
                number_format($item->money_total, 2),
                number_format($item->ok_total, 2),
                number_format($item->sum_total, 2)
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

    public function change()
    {
        $id = \YunShop::request()->id;
        $micro = MicroShop::find($id);
        $micro->level_id = \YunShop::request()->value;
        $micro->save();
    }
}