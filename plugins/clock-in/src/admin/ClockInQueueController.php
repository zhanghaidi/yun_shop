<?php

namespace Yunshop\ClockIn\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\ClockIn\models\ClockQueueModel;
use Yunshop\ClockIn\services\ClockInService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/08
 * Time:14:53
 */
class ClockInQueueController extends BaseController
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
        $list = ClockQueueModel::getList($search)->orderBy('created_at','desc')->paginate($this->pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());


        $pluginName = $this->_clockInService->get('plugin_name');

        return view('Yunshop\ClockIn::admin.clock-queue', [
            'list' => $list,
            'pager' => $pager,
            'total' => $list->total(),
            'pluginName' => $pluginName,
        ])->render();
    }

    public function export()
    {
        $pluginName = $this->_clockInService->get('plugin_name');

        $file_name = date('Ymdhis', time()) . $pluginName . '队列导出';
        $search = \YunShop::request()->get('search');
        $list = ClockQueueModel::getList($search)->orderBy('id', 'desc')
            ->get()
            ->toArray();
        $export_data[0] = [
            'ID',
            '时间',
            '前一天奖金池总金额',
            '奖金发放比例',
            '总发放金额',
            '前一天支付人数',
            '打卡人数',
            '未打卡人数'
        ];

        foreach ($list as $key => $item) {

            $export_data[$key + 1] = [
                $item['id'],
                $item['created_at'],
                $item['day_before_amount'],
                $item['rate'] . '%',
                $item['amount'],
                $item['pay_num'] . '人',
                $item['clock_in_num'] . '人',
                $item['not_clock_in_num'] . '人'

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