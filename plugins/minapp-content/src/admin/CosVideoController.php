<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\MinappContent\models\CosVideoModel;
use Yunshop\MinappContent\services\MinappContentService;

class CosVideoController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = CosVideoModel::select('*');
        if (isset($searchData['datelimit']['start']) && isset($searchData['datelimit']['end']) &&
            strtotime($searchData['datelimit']['start']) !== false && strtotime($searchData['datelimit']['end']) !== false
        ) {
            $list = $list->where('create_time', '>', strtotime($searchData['datelimit']['start']))
                ->where('create_time', '<', strtotime($searchData['datelimit']['end']) + 86400);
        } else {
            $searchData['datelimit']['start'] = date('Y-m-d H:i:s', strtotime('-1 year'));
            $searchData['datelimit']['end'] = date('Y-m-d H:i:s');
        }
        if (isset($searchData['keywords']) && intval($searchData['keywords']) > 0) {
            $list = $list->where('url', 'like', '%' . intval($searchData['keywords']) . '%');
        }

        $list = $list->orderBy('create_time', 'desc')
            ->orderBy('id', 'asc')->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        foreach ($list['data'] as &$v) {
            if (isset($v['porn_info'])) {
                $v['porn_info'] = json_decode($v['porn_info'], true);
            }
            if (isset($v['terrorist_info'])) {
                $v['terrorist_info'] = json_decode($v['terrorist_info'], true);
            }
            if (isset($v['politics_info'])) {
                $v['politics_info'] = json_decode($v['politics_info'], true);
            }
            if (isset($v['ads_info'])) {
                $v['ads_info'] = json_decode($v['ads_info'], true);
            }
        }
        unset($v);
        return view('Yunshop\MinappContent::admin.cos_video.list', [
            'pluginName' => MinappContentService::get('name'),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        CosVideoModel::where('id', $id)->delete();

        return $this->message('删除成功');
    }
}
