<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-05-14
 * Time: 14:25
 */

namespace Yunshop\Love\Backend\Modules\Love\Controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Yunshop\Love\Common\Models\LoveDividendLog;
use Yunshop\Love\Common\Services\CommonService;

class DividendLogController extends BaseController
{
    public function index()
    {
        $search = request()->search;

        $pageList = LoveDividendLog::getLog($search)->orderBy('id', 'desc')->paginate(20);
        $page = PaginationHelper::show($pageList->total(), $pageList->currentPage(), $pageList->perPage());

        return view('Yunshop\Love::Backend.Love.dividend-log', [
            'search'    => $search,
            'love_name' => $this->getLoveName(),
            'shopSet'   => \Setting::get('shop.member'),
            'pageList'      => $pageList,
            'page'      => $page,
        ])->render();
    }

    public function export()
    {
        $love_name = $this->getLoveName();
        $file_name = date('Ymdhis', time()) . $love_name . '分红记录导出';

        $search = request()->search;
        $list = LoveDividendLog::getLog($search)->orderBy('id', 'desc');

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($list, $export_page);

        $export_data[0] = [
            '会员ID',
            '分红',
        ];

        foreach ($export_model->builder_model as $key => $item) {
            $export_data[$key + 1] = [
                $item['member_id'],
                $item['dividend'],
            ];
        }
        $export_model->export($file_name, $export_data, \Request::query('route'));
    }

    public function getLoveName()
    {
        return CommonService::getLoveName();
    }
}