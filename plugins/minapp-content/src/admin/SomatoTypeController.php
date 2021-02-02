<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Goods;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\models\LabelModel;
use Yunshop\MinappContent\models\SomatoTypeModel;
use Yunshop\MinappContent\services\MinappContentService;
use Illuminate\Support\Facades\DB;

class SomatoTypeController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = SomatoTypeModel::where('uniacid', \YunShop::app()->uniacid);
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
        $list = $list->orderBy('id', 'asc')
            ->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.somato_type.list', [
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
                return $this->message('体质名称不能为空', '', 'danger');
            }
            $data['name'] = trim($data['name']);
            if (!isset($data['description']) || !isset(trim($data['description'])[0])) {
                return $this->message('体质描述不能为空', '', 'danger');
            }
            $data['description'] = trim($data['description']);

            if (isset($data['id'])) {
                $type = SomatoTypeModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($type->id)) {
                    return $this->message('体质ID参数错误', '', 'danger');
                }
            } else {
                $type = new SomatoTypeModel;
                $type->uniacid = \YunShop::app()->uniacid;
            }
            $type->name = $data['name'];
            $type->description = $data['description'];
            $type->symptom = isset($data['symptom']) ? implode(',', $data['symptom']) : '';
            $type->disease = isset($data['disease']) ? implode(',', $data['disease']) : '';
            $type->content = isset($data['content']) ? $data['content'] : '';
            $type->recommend_goods = isset($data['recommend_goods']) ? implode(',', $data['recommend_goods']) : '';
            $type->recommend_article = isset($data['recommend_article']) ? implode(',', $data['recommend_article']) : '';
            $type->recommend_acupotion = isset($data['recommend_acupotion']) ? implode(',', $data['recommend_acupotion']) : '';
            $type->save();
            if (!isset($type->id) || $type->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.somato-type.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = SomatoTypeModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('体质不存在或已被删除', '', 'danger');
            }

            $infoRs->symptom = explode(',', $infoRs->symptom);
            $infoRs->disease = explode(',', $infoRs->disease);
            $infoRs->recommend_goods = explode(',', $infoRs->recommend_goods);
            $infoRs->recommend_article = explode(',', $infoRs->recommend_article);
            $infoRs->recommend_acupotion = explode(',', $infoRs->recommend_acupotion);
        }

        $labelRs = LabelModel::select('id', 'name')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
            'type' => 2,
        ])->get()->toArray();

        $diseaseRs = DB::table("diagnostic_service_disease")->select('id', 'name')->where([
            'status' => 1,
        ])->get()->toArray();

        $acupointRs = AcupointModel::select('id', 'name')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->get()->toArray();

        // 芸众商品列表(去掉内购商品 category_id = 25)
        $goodsRs = Goods::select('yz_goods.id', 'title')
            ->join('yz_goods_category', 'yz_goods.id', '=', 'yz_goods_category.goods_id')
            ->where('yz_goods_category.category_id', '<>', 25)
            ->where('status', 1)
            ->orderBy('display_order', 'desc')->get();

        $articleRs = ArticleModel::select('id', 'title')->where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->get()->toArray();

        return view('Yunshop\MinappContent::admin.somato_type.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
            'label' => $labelRs,
            'acupoint' => $acupointRs,
            'goods' => $goodsRs,
            'article' => $articleRs,
            'disease' => $diseaseRs
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        SomatoTypeModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
