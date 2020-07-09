<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/16
 * Time: 下午5:38
 */

namespace Yunshop\Commission\admin;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Member;
use app\common\services\ExportService;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\Log;
use Yunshop\Commission\services\AgentService;
use Yunshop\Merchant\common\services\CenterUpgradeService;

class AgentController extends BaseController
{
    public function index()
    {
        $set = Setting::get('plugin.commission');

        $search = AgentService::getSearch(\YunShop::request()->search);

        $pageList = Agents::getAgents($search)->orderBy('id','desc')->paginate();

        $agents = AgentService::getAgentData($pageList->items());

        $page = PaginationHelper::show($pageList->total(), $pageList->currentPage(), $pageList->perPage());
        $agentLevels = AgentLevel::getLevels()->get();
        $defaultLevelName = AgentLevel::getDefaultLevelName();
        
        if(!$search['time']){
            $search['time']['start'] = date("Y-m-d H:i:s",time());
            $search['time']['end'] = date("Y-m-d H:i:s",time());
        }
        return view('Yunshop\Commission::admin.agent_list', [
            'set' => $set,
            'list' => $agents,
            'total' => $pageList->total(),
            'pager' => $page,
            'search' => $search,
            'agentlevels' => $agentLevels,
            'defaultlevelname' => $defaultLevelName
        ])->render();
    }

    public function detail()
    {
        $set = Setting::get('plugin.commission');

        $agentLevel = AgentLevel::getLevels()->get();

        $id = intval(\YunShop::request()->id);
        $agentModel = Agents::getAgentById($id)->first();
        if (!$agentModel) {
            return $this->message('无此分销商或已经删除', '', 'error');
        }

        // 修改之前的分销等级ID
        $before_level_id = $agentModel->agent_level_id;

        $requestAgnet = \YunShop::request()->agent;
        if ($requestAgnet) {
            //将数据赋值到model
            $agentModel->fill($requestAgnet);
            //字段检测
            $validator = $agentModel->validator();
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($agentModel->save()) {
                    // 等级修改记录
                    Log::addLog($before_level_id, $agentModel->agent_level_id, $agentModel, '后台修改');
                    //修改了记录，判断招商中心升级
                    if (app('plugins')->isEnabled('Merchant')) {
                        if ($before_level_id != $agentModel->agent_level_id) {
                            CenterUpgradeService::handle($agentModel->member_id);
                        }
                    }
                    //显示信息并跳转
                    return $this->message('分销商编辑成功', Url::absoluteWeb('plugin.commission.admin.agent.detail', ['id' => $id]));
                } else {
                    $this->error('分销商编辑失败');
                }
            }
        }

        return view('Yunshop\Commission::admin.agent_info', [
            'set' => $set,
            'agentLevel' => $agentLevel,
            'agentModel' => $agentModel,
            'defaultlevelname' => AgentLevel::getDefaultLevelName()
        ])->render();
    }

    public function lower()
    {
        $pageSize = 10;
        $set = Setting::get('plugin.commission');
        $agentlevels = AgentLevel::getLevels()->get();
        $id = intval(\YunShop::request()->id);
        $member = Member::getMemberById($id);

        $lower['total'] = Agents::getLower($id)->count();
        $lower['agent'] = Agents::getLower($id, '')->count();
        $lower['first'] = Agents::getLower($id, '1')->count();
        $lower['second'] = Agents::getLower($id, '2')->count();
        $lower['third'] = Agents::getLower($id, '3')->count();

        $search = AgentService::getSearch(\YunShop::request()->search);

        $list = Agents::getLower($id, '', $search)->paginate($pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        if(!$search['time']){
            $search['time']['start'] = date("Y-m-d H:i:s",time());
            $search['time']['end'] = date("Y-m-d H:i:s",time());
        }
        return view('Yunshop\Commission::admin.agent_lower', [
            'set' => $set,
            'search' => $search,
            'member' => $member,
            'lower' => $lower,
            'agentlevels' => $agentlevels,
            'defaultlevelname' => AgentLevel::getDefaultLevelName(),
            'total' => $list->total(),
            'list' => $list->items(),
            'pager' => $pager,
        ])->render();
    }

    public function black()
    {
        $id = intval(\YunShop::request()->id);
        $is_black = intval(\YunShop::request()->is_black);
        $agent = Agents::getAgentById($id)->first();
        if (!$agent) {
            return $this->message('无此分销商或已经删除', '', 'error');
        }
        $result = Agents::black($id, $is_black);
        if ($result) {
            return $this->message('黑名单设置成功', Url::absoluteWeb('plugin.commission.admin.agent.index'));
        } else {
            return $this->message('黑名单设置失败', '', 'error');
        }
    }

    public function deleted()
    {
        $id = intval(\YunShop::request()->id);
        $agent = Agents::getAgentById($id)->first();
        if (!$agent) {
            return $this->message('无此分销商或已经删除', '', 'error');
        }
        $result = Agents::deletedAgent($id);
        if ($result) {
            return $this->message('删除分销商成功', Url::absoluteWeb('plugin.commission.admin.agent.index'));
        } else {
            return $this->message('删除分销商失败', '', 'error');
        }
    }

    public function export()
    {
        $file_name = date('Ymdhis', time()) . '分销商导出';

        $search = AgentService::getSearch(\YunShop::request()->search);
        $builder = Agents::getAgents($search)->orderBy('id', 'desc');

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);

        //$search = AgentService::getSearch(\YunShop::request()->search);
        //$list = Agents::getAgents($search)->orderBy('id','desc')->get();
        $export_data[0] = [
            '会员ID',
            '推荐人',
            '昵称',
            '姓名/手机',
            '分销商等级/下级分销商人数',
            '累计佣金/已打款佣金',
            '关注',
            '黑名单'
        ];
        foreach ($export_model->builder_model as $key => $item) {
            $lowers = Agents::getLower($item['member_id'], '', true)->count();
            $level_name = $item['agent_level'] ? $item['agent_level']['name'] : AgentLevel::getDefaultLevelName();
            $export_data[$key + 1] = [
                $item['member_id'],
                $item->toParent->username ?: '总店',
                $item['member']['username'],
                $item['member']['realname'].'/'.$item['member']['mobile'],
                $level_name.'/'.$lowers,
                $item['commission_total'].'/'.$item['commission_pay'],
                $item['fans']['follow'] ? '已关注' : '未关注',
                $item['is_black'] ? '是' : '否',
            ];
        }
        $export_model->export($file_name, $export_data, \Request::query('route'));


//        $agents = AgentService::getAgentData($list)->toArray();
//        foreach ($agents as $key => $item) {
//            $export_data[$key + 1] = [
//                $item['member_id'],
//                $item['to_parent'] ? $item['to_parent']['nickname'] : '总店',
//                $item['member']['nickname'],
//                $item['member']['realname'].'/'.$item['member']['mobile'],
//                $item['agent_level'] ? ['name'] : '默认等级'.'/'.$item['lowers'],
//                $item['commission_total'].'/'.$item['commission_pay'],
//                $item['fans']['follow'] ? '已关注' : '未关注',
//                $item['is_black'] ? '是' : '否',
//            ];
//        }
//        \Excel::create($file_name, function ($excel) use ($export_data) {
//            // Set the title
//            $excel->setTitle('Office 2005 XLSX Document');
//
//            // Chain the setters
//            $excel->setCreator('芸众商城')
//                ->setLastModifiedBy("芸众商城")
//                ->setSubject("Office 2005 XLSX Test Document")
//                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
//                ->setKeywords("office 2005 openxml php")
//                ->setCategory("report file");
//
//            $excel->sheet('info', function ($sheet) use ($export_data) {
//                $sheet->rows($export_data);
//            });
//        })->export('xls');

    }
}