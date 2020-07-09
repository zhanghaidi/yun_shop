<?php

namespace Yunshop\ClockIn\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\ClockIn\models\ClockPayLogModel;
use Yunshop\ClockIn\models\ClockRewardLogModel;
use Yunshop\ClockIn\services\ClockInService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/09
 * Time:10:50
 */
class ClockInRewardLogController extends BaseController
{

    private $pageSize = 20;
    public $_clockInService;

    public function preAction()
    {
        $this->_clockInService = new ClockInService();
    }

    /**
     * @return string
     */
    public function index()
    {
        $search = \YunShop::request()->get('search');
        $list = ClockRewardLogModel::getList($search)->orderBy('created_at','desc')->paginate($this->pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        if (!$search['time']) {
            $search['time']['start'] = date("Y-m-d H:i:s", time());
            $search['time']['end'] = date("Y-m-d H:i:s", time());
            $search['is_time'] = 0;
        }

        $pluginName = $this->_clockInService->get('plugin_name');
        return view('Yunshop\ClockIn::admin.clock-reward-log', [
            'list' => $list,
            'pager' => $pager,
            'total' => $list->total(),
            'pluginName' => $pluginName,
            'search' => $search,
        ])->render();
    }

    public function export()
    {
        $pluginName = $this->_clockInService->get('plugin_name');
        $file_name = date('Ymdhis', time()) . $pluginName . '-奖励记录导出';
        $search = \YunShop::request()->get('search');
        $list = ClockRewardLogModel::getList($search)->orderBy('id', 'desc')
            ->get()
            ->toArray();
        $export_data[0] = [
            'ID',
            '时间',
            '会员',
            '奖励金额（元）'
        ];

        foreach ($list as $key => $item) {

            $export_data[$key + 1] = [
                $item['id'],
                $item['created_at'],
                $item['has_one_member']['nickname'],
                $item['amount']
            ];
        }

        \Excel::create($file_name, function ($excel) use ($export_data) {
            // Set the title
            $excel->setTitle('Office 2005 XLSX Document');

            // Chain the setters
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