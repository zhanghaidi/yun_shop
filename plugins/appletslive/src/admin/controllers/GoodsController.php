<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-12-18
 * Time: 16:00
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Appletslive\admin\controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Appletslive\common\services\BaseService;
use Yunshop\Appletslive\common\models\Goods;
use app\backend\modules\goods\models\Goods as YzGoods;
use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\Log;

class GoodsController extends BaseController
{
    // 商品列表
    public function index()
    {
        $input = \YunShop::request();
        $limit = 20;
        $tag = request()->get('tag', '');

        // 同步商品列表
        if ($tag == 'refresh') {
            if (!request()->ajax()) {
                return $this->message('非法操作', Url::absoluteWeb(''), 'danger');
            }
            $result = $this->refresh();
            return $this->successJson('商品库同步成功', $result);
        }

        // 处理搜索条件
        $where = [];
        if (isset($input->search)) {
            $search = $input->search;
            if (intval($search['id']) > 0) {
                $where[] = ['id', '=', intval($search['id'])];
            }
            if (trim($search['name']) !== '') {
                $where[] = ['name', 'like', '%' . trim($search['name']) . '%'];
            }
            if (trim($search['audit_status']) !== '') {
                $where[] = ['audit_status', '=', trim($search['audit_status'])];
            }
        }

        $list = Goods::where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        $goods_list = [];
        foreach ($list as $item) {
            $goods_list[] = ['id' => $item->id, 'audit_status' => $item->audit_status];
        }
        $goods_ids = array_column($goods_list, 'id');

        $audit_status = [];
        $service = new BaseService();
        $result = $service->getAuditStatus($goods_ids);
        if (is_array($result) && array_key_exists('errcode', $result) && $result['crrcode'] == 0) {
            foreach ($result['goods'] as $item) {
                $audit_status[$item['goods_id']] = $item['audit_status'];
            }
        }

        // dd(['goods_list' => $goods_list, 'audit_status' => $audit_status]);

        return view('Yunshop\Appletslive::admin.goods_index', [
            'list' => $list,
            'pager' => $pager,
            'request' => $input,
            'audit_status' => $audit_status,
        ])->render();
    }

    // 添加商品
    public function add()
    {
        if (request()->isMethod('post')) {

            $param = request()->all();
            $post_data = $param;
            unset($post_data['c']);
            unset($post_data['a']);
            unset($post_data['m']);
            unset($post_data['do']);
            unset($post_data['route']);

            // 必填项验证 - 商品名称
            if (!array_key_exists('name', $param) || $param['name'] == '') {
                return $this->errorJson('商品名称不能为空');
            }

            // 必填项验证 - 商品价格
            $price_type = $param['priceType'];
            if ($price_type == 1) {
                if (!array_key_exists('price', $param) || floatval($param['price']) <= 0) {
                    return $this->errorJson('商品价格设置不正确');
                }
            } else {

                if (!array_key_exists('price', $param) || floatval($param['price']) <= 0) {
                    return $this->errorJson('商品价格区间左边界设置不正确');
                }

                if (!array_key_exists('price2', $param) || floatval($param['price2']) <= 0) {
                    return $this->errorJson('商品价格区间右边界设置不正确');
                }

                if ($price_type == 2) {
                    if (floatval($param['price2']) < floatval($param['price'])) {
                        return $this->errorJson('商品价格区间右边界不能小于价格区间左边界');
                    }
                }
            }
            $post_data['priceType'] = intval($price_type);
            $post_data['price'] = floatval($param['price']);
            $post_data['price2'] = floatval($param['price2']);

            // 必填项验证 - 商品图片
            if (!array_key_exists('coverImgUrl', $param) || $param['coverImgUrl'] == '') {
                return $this->errorJson('商品图片不能为空');
            }
            // 上传临时素材
            $cover_img_path = (new BaseService())->downloadImgFromCos($param['coverImgUrl']);
            if ($cover_img_path['result_code'] != 0) {
                $msg = '图片获取失败:' . $cover_img_path['data'];
                return $this->errorJson($msg);
            }
            $upload_media = (new BaseService())->uploadMedia($cover_img_path['data']);
            if (array_key_exists('errcode', $upload_media)) {
                return $this->errorJson('上传临时素材失败:' . $upload_media['errmsg']);
            }
            $post_data['coverImgUrl'] = $upload_media['media_id'];
            $post_data['url'] = 'pages/shopping/detail/details?goods_id=' . $param['goodsId'];

            // 调用小程序接口添加商品并提审
            $result = (new BaseService())->addGoods($post_data);

            if ($result['errcode'] != 0) {
                $msg = $result['errmsg'];
                if ($result['errcode'] == 300018) {
                    $msg = '商品图片尺寸不得超过300像素*300像素';
                }
                if ($result['errcode'] == 300007) {
                    $msg = '商品跳转小程序页面地址不正确';
                }
                return $this->errorJson($msg, ['param' => $param, 'post' => $post_data, 'audit' => $result]);
            }

            $insert_data = [
                'id' => $result['goodsId'],
                'audit_id' => $result['auditId'],
                'goods_id' => $param['goodsId'],
                'name' => $post_data['name'],
                'price_type' => $post_data['priceType'],
                'price' => $post_data['price'],
                'price2' => $post_data['price2'],
                'url' => $post_data['url'],
            ];
            Goods::insert($insert_data);

            $this->refresh();
            return $this->errorJson('商品添加成功');
        }

        $exist = Goods::pluck('goods_id');
        $goods = YzGoods::whereNotIn('id', $exist)->get();

        return view('Yunshop\Appletslive::admin.goods_add', [
            'goods' => $goods,
        ])->render();
    }

    // 撤回审核
    public function resetaudit()
    {
        $id = request()->get('id', 0);
        $info = Goods::where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的商品ID', Url::absoluteWeb(''), 'danger');
        }

        $service = new BaseService();
        $result = $service->resetAudit($info['id'], $info['audit_id']);

        if ($result['errcode'] != 0) {
            return $this->message($result['errmsg'], Url::absoluteWeb(''), 'danger');
        }

        $this->refresh();
        Goods::where('id', $id)->update(['reset_audit' => 1]);
        return $this->message('撤销审核成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
    }

    // 重新提审
    public function audit()
    {
        $id = request()->get('id', 0);
        $info = Goods::where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的商品ID', Url::absoluteWeb(''), 'danger');
        }

        $service = new BaseService();
        $result = $service->audit($id);

        if ($result['errcode'] != 0) {
            return $this->message($result['errmsg'], Url::absoluteWeb(''), 'danger');
        }

        $this->refresh();
        Goods::where('id', $id)->update(['reset_audit' => 0, 'audit_id' => $result['auditId']]);
        return $this->message('提审成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
    }

    // 更新商品
    public function edit()
    {
        if (request()->isMethod('post')) {
            return $this->message('更新成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
        }

        $id = request()->get('id', 0);
        $info = Goods::where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的商品ID', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.room_edit', [
            'id' => $id,
            'info' => $info,
        ])->render();
    }

    // 删除商品
    public function del()
    {
        $id = request()->get('id', 0);
        $info = Goods::where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的商品ID', Url::absoluteWeb(''), 'danger');
        }

        $service = new BaseService();
        $result = $service->deleteGoods($info['id']);

        if ($result['errcode'] != 0) {
            return $this->message($result['errmsg'], Url::absoluteWeb(''), 'danger');
        }

        $this->refresh();
        return $this->message('删除成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
    }

    // 同步商品列表
    private function refresh()
    {
        $service = new BaseService();
        $present = [];
        for ($i = 0; $i < 4; $i++) {
            $temp = $service->getGoods($i);
            if (is_array($temp) && $temp['errcode'] == 0 && !empty($temp['goods'])) {
                array_walk($temp['goods'], function (&$item) use ($i) {
                    $item['auditStatus'] = $i;
                });
                $present = empty($present) ? $temp['goods'] : array_merge($present, $temp['goods']);
            }
        }
        $ids = array_column($present, 'goodsId');
        sort($ids);

        $sort_present = [];
        foreach ($ids as $id) {
            foreach ($present as $item) {
                if ($item['goodsId'] == $id) {
                    $sort_present[$id] = $item;
                    break;
                }
            }
        }

        // 查询数据库中已存在的商品列表
        $stored = Goods::where('id', '<=', max($ids))->limit(100)->get();

        $insert = [];
        $update = [];
        foreach ($sort_present as $spk => $spv) {
            $exist = false;
            foreach ($stored as $stk => $stv) {
                if ($stv['id'] == $spv['goodsId']) {
                    // 商品信息在数据库中存在，实时更新数据
                    $reset_audit = $stv['reset_audit'];
                    if ($spv['auditStatus'] != 0) {
                        $reset_audit = 0;
                    }
                    if ($stv['name'] != $spv['name'] || $stv['cover_img_url'] != $spv['coverImgUrl']
                        || $stv['price_type'] != $spv['priceType'] || $stv['price'] != $spv['price']
                        || $stv['price2'] != $spv['price2'] || $stv['url'] != $spv['url']
                        || $stv['audit_status'] != $spv['auditStatus'] || $stv['reset_audit'] != $reset_audit) {
                        array_push($update, [
                            'id' => $stv['id'],
                            'name' => $spv['name'],
                            'cover_img_url' => $spv['coverImgUrl'],
                            'price_type' => $spv['priceType'],
                            'price' => $spv['price'],
                            'price2' => $spv['price2'],
                            'url' => $spv['url'],
                            'audit_status' => $spv['auditStatus'],
                            'reset_audit' => $reset_audit,
                        ]);
                    }
                    $exist = true;
                    break;
                }
            }
            // 房间信息在数据库中不存在，实时记录数据
            if (!$exist) {
                array_push($insert, [
                    'id' => $spv['goodsId'],
                    'name' => $spv['name'],
                    'cover_img_url' => $spv['coverImgUrl'],
                    'price_type' => $spv['priceType'],
                    'price' => $spv['price'],
                    'price2' => $spv['price2'],
                    'url' => $spv['url'],
                    'audit_status' => $spv['auditStatus'],
                ]);
            }
        }

        $delete = [];
        foreach ($stored as $stk => $stv) {
            if (empty($sort_present[$stv['id']])) {
                array_push($delete, $stv['id']);
            }
        }

        if ($insert) {
            Goods::insert($insert);
        }
        if ($update) {
            foreach ($update as $item) {
                $id = $item['id'];
                $temp = $item;
                unset($temp['id']);
                Goods::where('id', $id)->update($temp);
            }
        }
        if ($delete) {
            Goods::whereIn('id', $delete)->delete();
        }

        return [
            'stored' => $stored,
            'present' => $sort_present,
            'insert' => $insert,
            'update' => $update,
            'delete' => $delete,
        ];
    }
}
