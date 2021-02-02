<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Goods;
use Yunshop\MinappContent\models\AcupointMerModel;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\models\MeridianModel;
use Yunshop\MinappContent\services\MinappContentService;

class AcupointController extends BaseController
{
    private $pageSize = 100;

    public function index()
    {
        $searchData = \YunShop::request()->search;
        $searchData = array_filter($searchData);

        $list = AcupointModel::where('uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['id'])) {
            $list = $list->where('id', $searchData['id']);
        }
        if (isset($searchData['keyword'])) {
            $list = $list->where(function ($query) use ($searchData) {
                $query->where('name', 'LIKE', '%' . $searchData['keyword'] . '%')
                    ->orWhere('chart', 'LIKE', '%' . $searchData['keyword'] . '%')
                    ->orWhere('jingluo', 'LIKE', '%' . $searchData['keyword'] . '%');
            });
        }
        if (isset($searchData['meridian_id']) && $searchData['meridian_id'] > 0) {
            $meridianRs = AcupointMerModel::select('id', 'acupoint_id')->where([
                'meridian_id' => $searchData['meridian_id'],
                'uniacid' => \YunShop::app()->uniacid,
            ])->get()->toArray();
            if (!isset($meridianRs[0])) {
                $meridianRs = [0];
            } else {
                $meridianRs = array_column($meridianRs, 'acupoint_id');
            }

            $list = $list->whereIn('id', $meridianRs);
        }
        $list = $list->orderBy('chart', 'asc')
            ->paginate($this->pageSize)->toArray();
        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['page'] = 'pages/acupoint/detail/detail?tid=' . $v['id'];
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.acupoint.list', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list['data'],
            'search' => $searchData,
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['name'])) {
                return $this->message('穴位名称不能为空', '', 'danger');
            }
            if (!isset($data['meridian_id']) || !is_array($data['meridian_id']) ||
                !isset($data['meridian_id'][0])
            ) {
                return $this->message('请选择穴位所属经络', '', 'danger');
            }
            $meridian = MeridianModel::select('id', 'discription')
                ->whereIn('id', $data['meridian_id'])
                ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
            if (!isset($meridian[0]['id'])) {
                return $this->message('选择穴位所属经络，数据错误', '', 'danger');
            }
            $data['jingluo'] = implode('、', array_column($meridian, 'discription'));
            $data['meridian_id'] = implode('、', array_column($meridian, 'id'));

            if (isset($data['id'])) {
                $acupoint = AcupointModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($acupoint->id)) {
                    return $this->message('穴位ID参数错误', '', 'danger');
                }
            } else {
                $acupoint = new AcupointModel;
                $acupoint->uniacid = \YunShop::app()->uniacid;
            }
            $acupoint->name = $data['name'];
            $acupoint->jingluo = $data['jingluo'];
            $acupoint->meridian_id = $data['meridian_id'];
            $acupoint->zh = isset($data['zh']) ? $data['zh'] : '';
            $acupoint->type = isset($data['type']) ? $data['type'] : '';
            $acupoint->get_position = isset($data['get_position']) ? $data['get_position'] : '';
            $acupoint->effect = isset($data['effect']) ? $data['effect'] : '';
            $acupoint->audio = isset($data['audio']) ? $data['audio'] : '';
            $acupoint->image = isset($data['image']) ? $data['image'] : '';
            $acupoint->video = isset($data['video']) ? $data['video'] : '';
            $acupoint->video_image_f = isset($data['video_image_f']) ? $data['video_image_f'] : '';
            $acupoint->video_image_s = isset($data['video_image_s']) ? $data['video_image_s'] : '';
            $acupoint->is_hot = isset($data['is_hot']) ? $data['is_hot'] : 0;
            $acupoint->chart = isset($data['chart']) ? strtoupper($data['chart']) : 0;
            $acupoint->recommend_goods = (isset($data['recommend_goods']) && is_array($data['recommend_goods'])) ? implode(',', $data['recommend_goods']) : '';
            $acupoint->recommend_article = (isset($data['recommend_article']) && is_array($data['recommend_article'])) ? implode(',', $data['recommend_article']) : '';
            $acupoint->save();
            if (!isset($acupoint->id) || $acupoint->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            $data['meridian_id'] = explode('、', $data['meridian_id']);
            $listRs = AcupointMerModel::select('id', 'meridian_id')
                ->where('acupoint_id', $acupoint->id)->get()->toArray();
            if (isset($listRs[0]['id'])) {
                $delIds = [];
                foreach ($listRs as $v) {
                    if (!in_array($v['meridian_id'], $data['meridian_id'])) {
                        $delIds[] = $v['id'];
                    }
                }
                if (isset($delIds[0])) {
                    AcupointMerModel::whereIn('id', $delIds)->delete();
                }

                $listRs = array_column($listRs, 'meridian_id');
                foreach ($data['meridian_id'] as $k => $v) {
                    if (in_array($v, $listRs)) {
                        unset($data['meridian_id'][$k]);
                    }
                }

                $data['meridian_id'] = array_values($data['meridian_id']);
            }
            if (isset($data['meridian_id'][0])) {
                $insertData = array();
                foreach ($data['meridian_id'] as $v) {
                    $insertData[] = [
                        'uniacid' => \YunShop::app()->uniacid,
                        'meridian_id' => $v,
                        'acupoint_id' => $acupoint->id,
                        'add_time' => time(),
                        'acupoint_name' => $acupoint->name,
                    ];
                }
                AcupointMerModel::insert($insertData);
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.acupoint.index'));
        }

        $zhData = \YunShop::request()->zh_data;
        if (isset($zhData[0]['id'])) {
            foreach ($zhData as $v) {
                if (!isset($v['id']) || $v['id'] <= 0 ||
                    !isset($v['zh']) || !isset($v['zh'][0])
                ) {
                    continue;
                }
                $v['zh'] = trim($v['zh']);
                AcupointModel::where([
                    'id' => $v['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->limit(1)->update([
                    'zh' => $v['zh'],
                ]);
            }
            return $this->successJson('拼音更新成功！');
        }

        $id = (int)\YunShop::request()->id;
        if ($id > 0) {
            $acupointInfo = AcupointModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            $acupointInfo->meridian_id = explode('、', $acupointInfo->meridian_id);
            $acupointInfo->recommend_goods = explode(',', $acupointInfo->recommend_goods);
            $acupointInfo->recommend_article = explode(',', $acupointInfo->recommend_article);
        }

        $meridian = MeridianModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->get();
        $articles = ArticleModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'status' => 1,
        ])->get();
        // 芸众商品列表(去掉内购商品 category_id = 25)
        $goods = Goods::select('yz_goods.id', 'title', 'thumb', 'price')
            ->join('yz_goods_category', 'yz_goods.id', '=', 'yz_goods_category.goods_id')
            ->where('yz_goods_category.category_id', '<>', 25)
            ->where('status', 1)
            ->orderBy('display_order', 'desc')->get();

        return view('Yunshop\MinappContent::admin.acupoint.edit', [
            'pluginName' => MinappContentService::get('name'),
            'meridian' => $meridian,
            'article' => $articles,
            'goods' => $goods,
            'info' => isset($acupointInfo) ? $acupointInfo : null,
        ]);
    }

    public function delete()
    {
        $id = (int)\YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        AcupointModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        AcupointMerModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'acupoint_id' => $id,
        ])->delete();

        return $this->message('删除成功');
    }
}
