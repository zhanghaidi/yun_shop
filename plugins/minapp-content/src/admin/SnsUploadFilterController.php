<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\MinappContent\models\SnsUploadFilterModel;
use Yunshop\MinappContent\models\UserModel;
use Yunshop\MinappContent\services\MinappContentService;

class SnsUploadFilterController extends BaseController
{
    private $pageSize = 30;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = SnsUploadFilterModel::where('uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['keywords']) && intval($searchData['keywords']) > 0) {
            $list = $list->where('user_id', 'like', '%' . intval($searchData['keywords']) . '%');
        }

        $list = $list->orderBy('id', 'desc')->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        $userIds = [];
        foreach ($list['data'] as $v) {
            $userIds[] = $v['user_id'];
        }
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (isset($userIds[0])) {
            $userRs = UserModel::select('ajy_uid', 'nickname', 'avatarurl')
                ->whereIn('ajy_uid', $userIds)->get()->toArray();
            foreach ($list['data'] as &$v1) {
                foreach ($userRs as $v2) {
                    if ($v1['user_id'] != $v2['ajy_uid']) {
                        continue;
                    }

                    $v1['nickname'] = $v2['nickname'];
                    $v1['avatarurl'] = $v2['avatarurl'];
                    break;
                }
            }
            unset($v1);
        }

        return view('Yunshop\MinappContent::admin.sns_upload_filter.list', [
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

        SnsUploadFilterModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
