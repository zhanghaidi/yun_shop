<?php

namespace Yunshop\CustomApp\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\CustomApp\models\CustomAppArticleModel;
use Yunshop\CustomApp\models\CustomAppArticleSortModel;
use Yunshop\CustomApp\services\CustomAppService;

class ArticleSortController extends BaseController
{
    private $pageSize = 10;

    public function index()
    {
        $listRs = CustomAppArticleSortModel::orderBy('id', 'desc')
            ->paginate($this->pageSize)->toArray();
        $sortIds = array_column($listRs['data'], 'id');
        if (isset($sortIds[0])) {
            $articleRs = CustomAppArticleModel::select('id', 'sort_id', 'updated_at')
                ->whereIn('sort_id', $sortIds)
                ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
            foreach ($listRs['data'] as $k1 => $v1) {
                foreach ($articleRs as $v2) {
                    if ($v1['id'] != $v2['sort_id']) {
                        continue;
                    }
                    $listRs['data'][$k1]['updated_at'] = $v2['updated_at'];
                    break;
                }
            }
        }

        $pager = PaginationHelper::show($listRs['total'], $listRs['current_page'], $this->pageSize);

        return view('Yunshop\CustomApp::admin.sort.index', [
            'pluginName' => CustomAppService::get('name'),
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

            $infoRs = CustomAppArticleSortModel::select('id')
                ->where('label', $data['label'])->first();
            if (isset($infoRs->id)) {
                return $this->message('唯一标识重复', '', 'danger');
            }

            $sort = new CustomAppArticleSortModel;
            $sort->name = $data['name'];
            $sort->label = $data['label'];
            $sort->save();

            return $this->message('保存成功', Url::absoluteWeb('plugin.custom-app.admin.article-sort.index'));
        }

        return view('Yunshop\CustomApp::admin.sort.add', [
            'pluginName' => CustomAppService::get('name'),
        ]);
    }
}
