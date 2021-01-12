<?php

namespace Yunshop\WechatComplaint\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\Member;
use Yunshop\WechatComplaint\models\ComplaintItemModel;
use Yunshop\WechatComplaint\models\ComplaintLogModel;
use Yunshop\WechatComplaint\services\WechatComplaintService;

class LogController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }

        $searchData = \YunShop::request()->search;

        $list = ComplaintLogModel::where([
            'project_id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ]);
        if (isset($searchData['time_range']['start']) && isset($searchData['time_range']['end']) &&
            strtotime($searchData['time_range']['start']) != false && strtotime($searchData['time_range']['end']) != false
        ) {
            $list = $list->where('created_at', '>=', strtotime($searchData['time_range']['start']))
                ->where('created_at', '<=', strtotime($searchData['time_range']['end']));
        }
        $list = $list->orderBy('id', 'desc')
            ->paginate($this->pageSize)->toArray();
        $memberIds = $itemIds = [];
        foreach ($list['data'] as $v) {
            $memberIds[] = $v['member_id'];
            $itemIds[] = $v['item_id'];
        }

        if (isset($memberIds[0])) {
            $memberRs = Member::select('uid', 'mobile', 'nickname', 'avatar')
                ->whereIn('uid', $memberIds)->get()->toArray();

            foreach ($list['data'] as &$v1) {
                foreach ($memberRs as $v2) {
                    if ($v1['member_id'] != $v2['uid']) {
                        continue;
                    }
                    $v1['mobile'] = $v2['mobile'];
                    $v1['nickname'] = $v2['nickname'];
                    $v1['avatar'] = $v2['avatar'];
                    break;
                }
            }
            unset($v1);
        }
        if (isset($itemIds[0])) {
            $itemRs = ComplaintItemModel::select('id', 'name')
                ->whereIn('id', $itemIds)
                ->withTrashed()->get()->toArray();

            foreach ($list['data'] as &$v1) {
                foreach ($itemRs as $v3) {
                    if ($v1['item_id'] != $v3['id']) {
                        continue;
                    }
                    $v1['item_name'] = $v3['name'];
                    break;
                }
            }
            unset($v1);
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\WechatComplaint::admin.log.index', [
            'pluginName' => WechatComplaintService::get('name'),
            'data' => $list['data'],
            'pager' => $pager,
            'id' => $id,
            'search' => $searchData,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;

        ComplaintLogModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
