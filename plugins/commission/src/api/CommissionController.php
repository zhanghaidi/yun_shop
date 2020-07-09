<?php


namespace Yunshop\Commission\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\CommissionManage;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\AgentLevel;

class CommissionController extends ApiController
{
    public function getCommission()
    {
        $manageSet = Setting::get('plugin.commission_manage');
        $request['total'] = CommissionOrder::getCommissionByMemberId()->sum('commission');
        $request['expect'] = CommissionOrder::getCommissionByMemberId(['0'])->sum('commission');
        $request['invalid'] = CommissionOrder::getCommissionByMemberId(['-1'])->sum('commission');
        $request['unliquidated'] = CommissionOrder::getCommissionByMemberId(['1'])->sum('commission');
        $request['liquidated'] = CommissionOrder::getCommissionByMemberId(['2'])->sum('commission');
        $request['not_yet_withdrawed'] = CommissionOrder::getCommissionByMemberId('', '0')->sum('commission');
        $request['withdrawed'] = CommissionOrder::getCommissionByMemberId('', '1')->sum('commission');
        $request['is_manage'] = $manageSet['is_manage'];
        $request['manage'] = CommissionManage::getManageByMemberId()->sum('manage_amount');

        if ($request) {
            return $this->successJson('获取数据成功!', $request);
        }
        return $this->errorJson('未检测到数据!', $request);
    }

    public function getCommissionList()
    {
        $type = \YunShop::request()->commission_type ? \YunShop::request()->commission_type : 1;
        $resultData = [];
        
        switch ($type) {
            case 1:
                $request = CommissionOrder::getCommissionByMemberId(['0'])->orderBy('id', 'desc')->paginate(20);
                break;
            case 2:
                $request = CommissionOrder::getCommissionByMemberId(['1'])->orderBy('id', 'desc')->paginate(20);
                break;
            case 3:
                $request = CommissionOrder::getCommissionByMemberId(['2'])->orderBy('id', 'desc')->paginate(20);
                break;
            case 4:
                $request = CommissionOrder::getCommissionByMemberId('', '0')->orderBy('id', 'desc')->paginate(20);
                break;
            case 5:
                $request = CommissionOrder::getCommissionByMemberId('', '1')->orderBy('id', 'desc')->paginate(20);
                break;
            case 6:
                $request = CommissionOrder::getCommissionByMemberId(['-1'])->orderBy('id', 'desc')->paginate(20);
                break;
        }
        
        if ($request) {
            $result = [];
            foreach ($request as $key => $item) {
                $order = $item->ordertable;
                $item = $item->toArray();
                $result[$key] = [
                    'id' => $item['id'],
                    'commission' => $item['commission'],
                    'created_at' => $item['created_at'],
                    'order_sn' => $order['order_sn'],
                    'price' => $order['price'],
                    'status' => $item['status'],
                ];
            }
         
            //传分页参数
            $resultData["data"] = $result;
            $resultData["total"] =$request->total();
            $resultData["per_page"] = $request->perPage();
            $resultData["current_page"] = $request->currentPage();
            $resultData["last_page"] = $request->lastPage();
            $resultData["next_page_url"] = $request->nextPageUrl();
            $resultData["prev_page_url"] = $request->previousPageUrl();
            $resultData["from"] = $request->firstItem();
            $resultData[ "to"] = $request->lastItem();
        
            return $this->successJson('获取数据成功!', $resultData);
        }
        return $this->errorJson('未检测到数据!');
    }

    public function getCommissionOrders()
    {
        $set = Setting::get('plugin.commission');
        $pageSize = 20;
        $status = \YunShop::request()->status !== "" ? \YunShop::request()->status : '';
        $request = CommissionOrder::getCommissionOrderByMemberId(\YunShop::app()->getMemberId(), $status)->paginate($pageSize);
        if ($request) {
            $data = [
                'orders' => $request->toArray(),
                'set' => [
                    'open_order_detail' => $set['open_order_detail'],
                    'open_order_buyer' => $set['open_order_buyer'],
                    'open_order_buyer_info' => $set['open_order_buyer_info'],
                ]
            ];
            foreach ($data['orders']['data'] as $key =>$itme){
                $data['orders']['data'][$key]['member']['wechat'] = $itme['member']['yz_member']['wechat'];
            }
            return $this->successJson('成功', $data);
        }
        return $this->errorJson('未检测到数据!', $request);
    }


    public function getAgentLevel()
    {

        $set = \Setting::get('plugin.commission');
        $request = Agents::getLevelByMemberId()
            ->where('member_id', \YunShop::app()->getMemberId())
            ->first();
        if (!$request) {
            return $this->errorJson('未检测到数据!');
        }
        $request = $request->toArray();
        if ($request['agent_level']) {
            $agentLevel = $request['agent_level'];
            $agentLevel['created_at'] = $request['created_at'];
        } else {
            $agentLevel = [
                'name' => AgentLevel::getDefaultLevelName(),
                'first_level' => $set['first_level'],
                'second_level' => $set['second_level'],
                'third_level' => $set['third_level'],
                'created_at' => $request['created_at']
            ];
        }
        if ($agentLevel) {
            return $this->successJson('获取数据成功!', $agentLevel);
        }
        return $this->errorJson('未检测到数据!', $agentLevel);
    }

    public function getManage()
    {
        // plugin.commission.api.commission.get-manage
        $request = CommissionManage::getManageByMemberId()->get();

        if ($request) {
            return $this->successJson('获取数据成功!', $request);
        }
        return $this->errorJson('未检测到数据!', $request);
    }

}
