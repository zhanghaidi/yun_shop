<?php

namespace Yunshop\VideoDemand\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\Member;
use Yunshop\VideoDemand\models\LecturerModel;
use Yunshop\VideoDemand\services\LecturerRewardLogService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/09
 * Time: 下午2:01
 */
class LecturerController extends BaseController
{
    protected $pageSize = 10;

    public function index()
    {
        $search = \YunShop::request()->get('search');

        $list = LecturerModel::getLecturerList($search)->orderBy('id', 'desc')->paginate($this->pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        $list = LecturerRewardLogService::getLecturerReward($list);

        return view('Yunshop\VideoDemand::admin.lecturer', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
            'total'=>$list->total()
        ])->render();
    }

    public function add()
    {
        $lecturerData = \YunShop::request()->lecturer;

        if ($lecturerData) {

            $agent = LecturerModel::getLecturerByMemberId($lecturerData['member_id'])->first();
            if ($agent) {
                return $this->message('添加失败,此会员已是讲师', '', 'error');
            }
            $lecturerModel = new LecturerModel();
            //将数据赋值到model
            $lecturerModel->setRawAttributes($lecturerData);
            //其他字段赋值
            $lecturerModel->uniacid = \YunShop::app()->uniacid;
            //字段检测
            $validator = $lecturerModel->validator($lecturerModel->getAttributes());
            if ($validator->fails()) {
                //检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($lecturerModel->save()) {
                    // 发送消息
                    $member =Member::getMemberByUid($lecturerModel->member_id)->with('hasOneFans')->first();
//                    MessageService::becomeAgent($agentModel, $member->hasOneFans);
                    //显示信息并跳转
                    return $this->message('添加成功', yzWebUrl('plugin.video-demand.admin.lecturer.index'));
                } else {
                    return $this->message('添加失败', '', 'error');
                }
            }
        }
        return view('Yunshop\VideoDemand::admin.lecturer-add', [

        ])->render();
    }

    public function export()
    {
        $file_name = date('Ymdhis', time()) . '讲师导出';
        $search = \YunShop::request()->get('search');
        $list = LecturerModel::getLecturerList($search)->orderBy('id', 'desc')->get();

        $list = LecturerRewardLogService::getLecturerReward($list)->toArray();

        $export_data[0] = [
            'ID',
            '会员',
            '姓名/手机',
            '成为时间',
            '课程商品数',
            '累计结算分红金额',
            '累计未结算分红金额',
        ];

        foreach ($list as $key => $item) {

            $export_data[$key + 1] = [
                $item['id'],
                $item['has_one_member']['nickname'],
                $item['real_name'].'/'.$item['mobile'],
                $item['created_at'],
                count($item['has_many_course_goods']),
                $item['statement'],
                $item['not_statement']
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