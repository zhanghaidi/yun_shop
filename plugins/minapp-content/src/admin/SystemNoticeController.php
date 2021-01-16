<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Illuminate\Support\Facades\DB;
use Yunshop\MinappContent\models\SystemNoticeModel;
use Yunshop\MinappContent\services\MinappContentService;

class SystemNoticeController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = SystemNoticeModel::where('uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['datelimit']['start']) && isset($searchData['datelimit']['end']) &&
            strtotime($searchData['datelimit']['start']) !== false && strtotime($searchData['datelimit']['end']) !== false
        ) {
            $list = $list->where('create_time', '>', strtotime($searchData['datelimit']['start']))
                ->where('create_time', '<', strtotime($searchData['datelimit']['end']) + 86400);
        } else {
            $searchData['datelimit']['start'] = date('Y-m-d H:i:s', strtotime('-1 year'));
            $searchData['datelimit']['end'] = date('Y-m-d H:i:s');
        }
        if (isset($searchData['keywords']) && isset(trim($searchData['keywords'])[0])) {
            $searchData['keywords'] = trim($searchData['keywords']);
            $list = $list->where(function ($query) use ($searchData) {
                $query->where('title', 'LIKE', '%' . $searchData['keywords'] . '%')
                    ->orWhere('content', 'LIKE', '%' . $searchData['keywords'] . '%');
            });
        }
        $list = $list->orderBy('list_order', 'desc')
            ->orderBy('id', 'asc')
            ->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.system_notice.list', [
            'pluginName' => MinappContentService::get('name'),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['content']) || !isset(trim($data['content'])[0])) {
                return $this->message('通知内容不能为空', '', 'danger');
            }
            $data['content'] = trim($data['content']);

            if (isset($data['id'])) {
                $notice = SystemNoticeModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($notice->id)) {
                    return $this->message('系统通知ID参数错误', '', 'danger');
                }
            } else {
                $notice = new SystemNoticeModel;
                $notice->uniacid = \YunShop::app()->uniacid;
            }
            $notice->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $notice->title = isset($data['title']) ? $data['title'] : '';
            $notice->content = $data['content'];
            $notice->status = isset($data['status']) ? $data['status'] : 0;
            $notice->jumpurl = isset($data['jumpurl']) ? $data['jumpurl'] : '';
            $notice->appid = isset($data['appid']) ? $data['appid'] : '';
            $notice->jumptype = isset($data['jumptype']) ? $data['jumptype'] : 0;
            $notice->save();
            if (!isset($notice->id) || $notice->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.system-notice.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = SystemNoticeModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('通知不存在或已被删除', '', 'danger');
            }
        }

        $minappRs = DB::table('account_wxapp')->select('key', 'name')->get()->toArray();

        return view('Yunshop\MinappContent::admin.system_notice.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
            'app' => $minappRs,
        ]);
    }

    public function status()
    {
        $id = \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }
        $infoRs = SystemNoticeModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('参数ID数据未找到', '', 'danger');
        }
        $message = '';
        if ($infoRs->status == 1) {
            $infoRs->status = 0;
            $message = '隐藏成功';
        } else {
            $infoRs->status = 1;
            $message = '显示成功';
        }
        $infoRs->save();

        return $this->message($message);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        SystemNoticeModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
