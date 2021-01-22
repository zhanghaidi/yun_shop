<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Illuminate\Support\Facades\DB;
use Yunshop\MinappContent\models\SystemCategoryModel;
use Yunshop\MinappContent\services\MinappContentService;

class SystemCategoryController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = SystemCategoryModel::where('uniacid', \YunShop::app()->uniacid);
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
            $list = $list->where('name', 'like', '%' . $searchData['keywords'] . '%');
        }
        $list = $list->orderBy('list_order', 'desc')
            ->orderBy('id', 'asc')
            ->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.system_category.list', [
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
            if (!isset($data['name']) || !isset(trim($data['name'])[0])) {
                return $this->message('分类名称不能为空', '', 'danger');
            }
            $data['name'] = trim($data['name']);

            if (isset($data['id'])) {
                $category = SystemCategoryModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($category->id)) {
                    return $this->message('系统分类ID参数错误', '', 'danger');
                }
            } else {
                $category = new SystemCategoryModel;
                $category->uniacid = \YunShop::app()->uniacid;
            }
            $category->name = $data['name'];
            $category->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $category->image = isset($data['image']) ? $data['image'] : '';
            $category->status = isset($data['status']) ? $data['status'] : 0;
            $category->jumpurl = isset($data['jumpurl']) ? $data['jumpurl'] : '';
            $category->appid = isset($data['appid']) ? $data['appid'] : '';
            $category->jumptype = isset($data['jumptype']) ? $data['jumptype'] : 0;
            $category->save();
            if (!isset($category->id) || $category->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.system-category.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = SystemCategoryModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('系统分类不存在或已被删除', '', 'danger');
            }
        }

        $minappRs = DB::table('account_wxapp')->select('key', 'name')->get()->toArray();

        return view('Yunshop\MinappContent::admin.system_category.edit', [
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
        $infoRs = SystemCategoryModel::where([
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

        SystemCategoryModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
