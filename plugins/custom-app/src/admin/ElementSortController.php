<?php

namespace Yunshop\CustomApp\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\CustomApp\models\CustomAppElementModel;
use Yunshop\CustomApp\models\CustomAppElementSortModel;
use Yunshop\CustomApp\services\CustomAppService;

class ElementSortController extends BaseController
{
    private $pageSize = 10;

    public function index()
    {
        $searchData = \YunShop::request()->search;
        $searchData = array_filter($searchData);

        $listRs = CustomAppElementSortModel::orderBy('id', 'desc');
        if (isset($searchData['name'])) {
            $listRs = $listRs->where('name', 'like', '%' . $searchData['name'] . '%');
        }

        $listRs = $listRs->paginate($this->pageSize)->toArray();
        $sortIds = array_column($listRs['data'], 'id');
        if (isset($sortIds[0])) {
            $elementRs = CustomAppElementModel::select('id', 'sort_id', 'content', 'updated_at')
                ->whereIn('sort_id', $sortIds)
                ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
            foreach ($listRs['data'] as $k1 => $v1) {
                foreach ($elementRs as $v2) {
                    if ($v1['id'] != $v2['sort_id']) {
                        continue;
                    }
                    $listRs['data'][$k1]['content'] = $v2['content'];
                    $listRs['data'][$k1]['updated_at'] = $v2['updated_at'];
                    break;
                }
            }
        }

        $pager = PaginationHelper::show($listRs['total'], $listRs['current_page'], $this->pageSize);

        return view('Yunshop\CustomApp::admin.element-sort.index', [
            'pluginName' => CustomAppService::get('name'),
            'search' => $searchData,
            'data' => $listRs['data'],
            'pager' => $pager,
        ]);
    }

    public function add()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['name']) || !isset($data['label'])) {
                return $this->message('请填写名称和标识', '', 'danger');
            }
            if (!isset($data['type']) || !in_array($data['type'], [1, 2, 3, 4])) {
                return $this->message('请选择页面元素的值类型', '', 'danger');
            }

            $infoRs = CustomAppElementSortModel::select('id')
                ->where('label', $data['label'])->first();
            if (isset($infoRs->id)) {
                return $this->message('唯一标识重复', '', 'danger');
            }

            $sort = new CustomAppElementSortModel;
            $sort->name = $data['name'];
            $sort->label = $data['label'];
            $sort->type = $data['type'];
            $sort->save();

            return $this->message('保存成功', Url::absoluteWeb('plugin.custom-app.admin.element.edit', ['id' => $sort->id]));
        }

        return view('Yunshop\CustomApp::admin.element-sort.add', [
            'pluginName' => CustomAppService::get('name'),
        ]);
    }
}
