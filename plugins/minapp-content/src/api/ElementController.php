<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use Yunshop\CustomApp\models\CustomAppArticleModel;
use Yunshop\CustomApp\models\CustomAppArticleSortModel;
use Yunshop\CustomApp\models\CustomAppElementModel;
use Yunshop\CustomApp\models\CustomAppElementSortModel;

class ElementController extends ApiController
{
    protected $publicAction = ['article', 'index'];

    public function article()
    {
        $id = \YunShop::request()->id;

        $sortRs = CustomAppArticleSortModel::select('id', 'name')
            ->where('label', $id)->first();
        if (!isset($sortRs->id)) {
            return $this->errorJson('数据不存在');
        }

        $articleRs = CustomAppArticleModel::select('id', 'content')->where([
            'sort_id' => $sortRs->id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        return $this->successJson('成功', [
            'id' => $sortRs->id,
            'name' => $sortRs->name,
            'content' => isset($articleRs->content) ? $articleRs->content : '',
        ]);
    }
}
