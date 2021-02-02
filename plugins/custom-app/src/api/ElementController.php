<?php

namespace Yunshop\CustomApp\api;

use app\common\components\ApiController;
use Yunshop\CustomApp\models\CustomAppArticleModel;
use Yunshop\CustomApp\models\CustomAppArticleSortModel;
use Yunshop\CustomApp\models\CustomAppElementModel;
use Yunshop\CustomApp\models\CustomAppElementSortModel;

class ElementController extends ApiController
{
    protected $publicAction = ['article', 'index'];
    protected $ignoreAction = ['article', 'index'];

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

    public function index()
    {
        $id = \YunShop::request()->id;
        $id = json_decode($id, true);
        if ($id === null) {
            return $this->errorJson('参数错误');
        }
        if (!is_array($id)) {
            $id = [$id];
        }

        $sortRs = CustomAppElementSortModel::select('id', 'label', 'type')
            ->whereIn('label', $id)->get()->toArray();
        if (!isset($sortRs[0]['id'])) {
            return $this->errorJson('数据不存在');
        }
        $sortIds = array_column($sortRs, 'id');

        $elementRs = CustomAppElementModel::select('id', 'sort_id', 'content')
            ->whereIn('sort_id', $sortIds)
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $return = [];
        foreach ($id as $v1) {
            $temp = [
                'id' => $v1,
                'content' => '',
            ];

            foreach ($sortRs as $v2) {
                if ($v1 != $v2['label']) {
                    continue;
                }

                foreach ($elementRs as $v3) {
                    if ($v2['id'] != $v3['sort_id']) {
                        continue;
                    }
                    if ($v2['type'] == 2) {
                        $v3['content'] = yz_tomedia($v3['content']);
                    }
                    if ($v2['type'] == 5) {
                        $v3['content'] = yz_tomedia($v3['content']);
                    }

                    if (in_array($v2['type'], [3, 4])) {
                        $v3['content'] = json_decode($v3['content'], true);

                        if ($v2['type'] == 4) {
                            foreach ($v3['content'] as $k4 => $v4) {
                                $v3['content'][$k4] = yz_tomedia($v4);
                            }
                        }
                    }
                    $temp['content'] = $v3['content'];
                    break;
                }
                break;
            }
            $return[] = $temp;
        }
        return $this->successJson('成功', $return);
    }
}
