<?php

namespace Yunshop\LeaseToy\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\backend\modules\member\models\MemberLevel;
use Yunshop\LeaseToy\models\LeaseMemberModel;
use Yunshop\LeaseToy\models\DepositRecordModel;
use Yunshop\LeaseToy\models\LeaseOrderModel;
use Carbon\Carbon;



/**
* 
*/
class DepositRecordController extends BaseController
{
    protected $pageSize = 15;

   public function index()
   {
        $search = \Yunshop::request()->get('search');

        $list = LeaseMemberModel::searchRecord($search)->paginate($this->pageSize);

        $list = $this->recordMap($list);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());        
        $levels = MemberLevel::getMemberLevelList();

        return view('Yunshop\LeaseToy::admin.deposit-record-list', [
            'list' => $list->toArray(),
            'levels' => $levels,
            'pager' => $pager,
            'search' => $search,
            'total' => $list->total(),
        ])->render();
   }

   private function recordMap($objs)
   {
        if (empty($objs)) return $objs;
        $objs->map(function ($obj) {
            $obj->levelname = MemberLevel::getMemberLevelNameById($obj->belongsToMember->yzMember->level_id);
            if (empty($obj->levelname)) {
                $obj->levelname = '普通会员';
            }
           
            unset($obj->belongsToMember->yzMember);
        });


        return $objs;
   }

   public function detail()
   {
        $search = \Yunshop::request()->get('search');

        $id = \Yunshop::request()->get('lease_id') ? : 0;

        $list = LeaseOrderModel::getLeaseMemberOrder($id, $search)->paginate($this->pageSize);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());        

        $list = $this->recordMap($list);
        $total = LeaseOrderModel::toTal($id);

        $starttime = time();
        $endtime = time();

        if (isset($search['searchtime']) &&  $search['searchtime'] == 1) {
            $starttime = strtotime($search['times']['start']);
            $endtime = strtotime($search['times']['end']);
        }
// dd($list->toArray());

        $levels = MemberLevel::getMemberLevelList();
        return view('Yunshop\LeaseToy::admin.deposit-record-detail', [
            'list' => $list->toArray(),
            // 'pager' => $pager,
            'search' => $search,
            'levels' => $levels,
            'starttime'=> $starttime,
            'endtime' => $endtime,
            'lease' => [
                'lease_id' => $id,
                'total'  => $total,
                'frozen' => '冻结',
                'return' => '已退还',
            ],
        ])->render();

   }

   public function export()
   {
        $file_name = date('Ymdhis', time()).'押金管理';
        $search = \Yunshop::request()->get('search');

        $list = LeaseMemberModel::searchRecord($search)->orderBy('id', 'desc')->get();

        $list = $this->recordMap($list);

         $export_data[0] = [
            'ID',
            '会员',
            '姓名/手机',
            '等级',
            '押金',
        ];

        foreach ($list as $key => $item) {

            $export_data[$key + 1] = [
                $item->id,
                $item->belongsToMember->nickname,
                $item->belongsToMember->realname.'/'.$item->belongsToMember->mobile,
                $item->levelname,
                $item->total_deposit,
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
            })->export('xls');
        });

   }

   public function detailExport()
   {

        $search = \Yunshop::request()->get('search');

        $id = \Yunshop::request()->get('lease_id') ? : 0;

        $list = LeaseOrderModel::getLeaseMemberOrder($id, $search)->get();

        // $list = $this->recordMap($list);

        $file_name = date('Ymd', time()).'会员：'.$list[0]->belongsToMember->nickname.'——押金明细';
        $export_data[0] = [
            '序号',
            '订单编号',
            '押金(元)',
            '退还押金(元)',
            '状态',
            '时间（开租|退还）',
        ];

        foreach ($list as $key => $item) {

            $export_data[$key + 1] = [
                $item->id,
                $item->order_sn,
                $item->deposit_total,
                $item->return_deposit,
                ($item->return_status == 3) ? '已退还' : '冻结',
                ($item->return_status == 3) ? $item->return_time : $item->start_time,
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
            })->export('xls');
        });
   }
}