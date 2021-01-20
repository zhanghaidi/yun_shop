<?php

namespace Yunshop\VideoDemand\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\VideoDemand\models\LecturerRewardLogModel;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/09
 * Time: 下午2:01
 */
class LecturerRewardController extends BaseController
{
    protected $pageSize = 10;

    public function index()
    {
        $search = \YunShop::request()->get('search');

        $list = LecturerRewardLogModel::getLecturerRewardList($search)->orderBy('id', 'desc')->paginate($this->pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        if (!$search['time']) {
            $search['time']['start'] = date("Y-m-d H:i:s", time());
            $search['time']['end'] = date("Y-m-d H:i:s", time());
            $search['is_time'] = 0;
        }
        return view('Yunshop\VideoDemand::admin.lecturer-reward', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
            'total'=>$list->total()
        ])->render();
    }


    public function export()
    {
        // dd('讲师分红导出');
        $file_name = date('YmdHis', time()) . '讲师分红导出';
        $search = \Yunshop::request()->get('search');

        $list = LecturerRewardLogModel::getLecturerRewardList($search)->orderBy('id', 'desc')->get()->toArray();
        $export_top = [
            'ID',
            '订单号',
            '讲师名称',
            '课程名称',
            '订单金额',
            '分红金额',
            '业务类型',
            '分红状态',
        ];

        foreach ($list as $key => $value) {
            $export_data[] = [
                $value['id'],
                $value['order_sn'],
                $value['has_one_lecturer']['real_name'],
                $value['has_one_course']['goods_title'],
                $value['order_price'],
                $value['amount'],
                $value['reward_type_name'],
                $value['status_name'],

            ];
        }
        array_unshift($export_data, $export_top);

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
            })->export('xls');
        });

    }

}