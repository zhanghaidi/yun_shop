<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\services\MinappContentService;
use Illuminate\Support\Facades\DB;

class SportClockController extends BaseController
{
    private $pageSize = 20;

    /**
     * 运动打卡设置
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed|\think\response\View
     */
    public function step()
    {
        $uniacid = \YunShop::app()->uniacid;
        $info = DB::table('diagnostic_service_system_step')->where('uniacid', $uniacid)->first();
        if (request()->isMethod('post')) {
            $param = request()->all();
            $data = [
                'uniacid' => $uniacid,
                'least_step' => intval(trim($param['least_step'])),
                'ratio' => floatval(trim($param['ratio'])),
                'discuss_point' => intval(trim($param['discuss_point'])),
                'health_gold_rules' => trim($param['health_gold_rules'])
            ];
            if (!$info) {
                $res = DB::table('diagnostic_service_system_step')->insert($data);
            } else {
                $res = DB::table('diagnostic_service_system_step')->where('uniacid', $uniacid)->where('id', $info['id'])->update($data);
            }
            if ($res) {
                return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.sport-clock.step'));
            } else {
                return $this->message('修改失败', '', 'danger');

            }
        }
        return view('Yunshop\MinappContent::admin.sport_clock.step', [
            'pluginName' => MinappContentService::get('name'),
            'info' => $info,
            'type' => 'step'
        ]);
    }

    /**
     * 步数兑换记录
     */
    public function stepExchangeList()
    {
        $input = \YunShop::request();
        $uniacid = \YunShop::app()->uniacid;

        $where[] = ['diagnostic_service_step_exchange.uniacid', '=', $uniacid];
        $where_between = ['diagnostic_service_step_exchange.create_time', [0, strtotime('20991231')]];
        if (isset($input->search)) {
            $search = $input->search;
            if (intval($search['user_id']) > 0) {
                $where[] = ['diagnostic_service_step_exchange.user_id', '=', intval($search['user_id'])];
            }
            if (trim($search['nickname']) !== '') {
                $where[] = ['diagnostic_service_user.nickname', 'like', '%' . trim($search['nickname']) . '%'];
            }
            if ($search['search_time'] == 1) {
                $where_between[0] = 'diagnostic_service_step_exchange.create_time';
                $where_between[1] =  [strtotime($search['time']['start']), strtotime($search['time']['end'])];
            }
        }

        $exchanges = DB::table('diagnostic_service_step_exchange')
            ->leftjoin('diagnostic_service_user', 'diagnostic_service_user.ajy_uid', '=', 'diagnostic_service_step_exchange.user_id')
            ->select('diagnostic_service_user.nickname', 'diagnostic_service_user.avatarurl as avatar', 'diagnostic_service_step_exchange.*')
            ->where($where)
            ->whereBetween($where_between[0], $where_between[1])
            ->orderBy('id', 'desc')
            ->paginate($this->pageSize);

        $pager = PaginationHelper::show($exchanges->total(), $exchanges->currentPage(), $exchanges->perPage());

        return view('Yunshop\MinappContent::admin.sport_clock.step_exchange_list', [
            'pluginName' => MinappContentService::get('name'),
            'type' => 'step_exchange_list',
            'exchanges' => $exchanges,
            'pager' => $pager,
            'request' => $input,
        ]);
    }
}
