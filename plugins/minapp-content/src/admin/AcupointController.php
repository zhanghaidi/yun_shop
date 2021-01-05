<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\CustomApp\models\CustomAppArticleModel;
use Yunshop\CustomApp\models\CustomAppArticleSortModel;
use Yunshop\CustomApp\services\CustomAppService;
use Yunshop\MinappContent\models\AcupointModel;

class AcupointController extends BaseController
{
    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['sort_id']) || $data['sort_id'] <= 0) {
                return $this->message('程序错误，请联系开发人员', '', 'danger');
            }

            $sortRs = CustomAppArticleSortModel::select('id')
                ->where('id', $data['sort_id'])->first();
            if (!isset($sortRs->id)) {
                return $this->message('页面数据错误，请联系开发', '', 'danger');
            }

            $articleRs = CustomAppArticleModel::where([
                'sort_id' => $data['sort_id'],
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($articleRs->id)) {
                $articleRs = new CustomAppArticleModel;
                $articleRs->uniacid = \YunShop::app()->uniacid;
                $articleRs->sort_id = $data['sort_id'];
            }
            $articleRs->content = html_entity_decode(isset($data['content']) ? $data['content'] : '');
            $articleRs->save();

            return $this->message('保存成功', Url::absoluteWeb('plugin.custom-app.admin.article-sort.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('页面选择错误，请联系开发', '', 'danger');
        }

        $sortRs = CustomAppArticleSortModel::select('id')
            ->where('id', $id)->first();
        if (!isset($sortRs->id)) {
            return $this->message('页面数据错误，请联系开发', '', 'danger');
        }

        $articleRs = CustomAppArticleModel::where([
            'sort_id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();

        return view('Yunshop\CustomApp::admin.article.edit', [
            'pluginName' => CustomAppService::get('name'),
            'data' => $articleRs,
            'id' => $id,
        ]);
    }
}
