<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\models\ArticleCategoryModel;
use Yunshop\MinappContent\services\MinappContentService;

class ArticleCategoryController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $searchData = \YunShop::request()->search;
        $searchData = array_filter($searchData);

        $list = ArticleCategoryModel::where('uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['keywords'])) {
            $searchData['keywords'] = trim($searchData['keywords']);
            $list = $list->where('name', 'like', '%' . $searchData['keywords'] . '%');
        }
        $list = $list->orderBy('list_order', 'desc')
            ->orderBy('id', 'asc')
            ->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.article_category.list', [
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
            if (!isset($data['name'])) {
                return $this->message('栏目名称不能为空', '', 'danger');
            }

            if (isset($data['id'])) {
                $category = ArticleCategoryModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($category->id)) {
                    return $this->message('栏目ID参数错误', '', 'danger');
                }
            } else {
                $category = new ArticleCategoryModel;
                $category->uniacid = \YunShop::app()->uniacid;
            }
            $category->name = $data['name'];
            $category->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $category->image = isset($data['image']) ? trim($data['image']) : '';
            $category->status = isset($data['status']) ? intval($data['status']) : 0;
            $category->type = isset($data['type']) ? intval($data['type']) : 0;
            $category->save();
            if (!isset($category->id) || $category->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.article-category.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = ArticleCategoryModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
        }

        return view('Yunshop\MinappContent::admin.article_category.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        ArticleCategoryModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
