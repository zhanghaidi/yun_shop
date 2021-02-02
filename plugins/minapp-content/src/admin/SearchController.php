<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use Yunshop\MinappContent\models\SearchModel;
use Yunshop\MinappContent\services\MinappContentService;

class SearchController extends BaseController
{
    public function index()
    {
        return view('Yunshop\MinappContent::admin.search.index', [
            'pluginName' => MinappContentService::get('name'),
        ]);
    }

    public function lists()
    {
        $listRs = SearchModel::selectRaw('keywords as name, sum(search_nums) as value')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->groupBy('keywords')->get()->toArray();
        return $this->successJson('成功', $listRs);
    }
}
