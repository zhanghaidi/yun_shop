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

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\backend\modules\goods\models\Brand;
use Yunshop\Appletslive\common\services\BaseService;
use Yunshop\Appletslive\common\models\Goods;
use app\backend\modules\goods\models\Goods as YzGoods;
use app\common\helpers\PaginationHelper;

class GoodsController extends BaseController
{
    protected $goodsUrlPrefix = 'pages/shopping/detail/details?goods_id=';

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
            $result = Goods::refresh();
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
            unset($param['c']);
            unset($param['a']);
            unset($param['m']);
            unset($param['do']);
            unset($param['route']);
            $post_data = $param;

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
            $post_data['url'] = $this->goodsUrlPrefix . $param['goodsId'];

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
                'cover_img_origin' => $param['coverImgUrl'],
                'url' => $post_data['url'],
            ];
            Goods::insert($insert_data);

            Goods::refresh();
            return $this->successJson('商品添加成功');
        }

        // 处理搜索条件
        $input = \YunShop::request();
        $where = [];
        if (isset($input->search)) {
            $search = $input->search;
            if (trim($search['brand_id']) !== '') {
                $where[] = ['brand_id', '=', trim($search['brand_id'])];
            }
            if (trim($search['title']) !== '') {
                $where[] = ['title', 'like', '%' . trim($search['title']) . '%'];
            }
        }

        $limit = 20;
        $exist = Goods::pluck('goods_id');
        $brand = Brand::where('uniacid', 39)->pluck('name', 'id');
        $goods = YzGoods::whereNotIn('id', $exist)->where($where)->paginate($limit);
        $pager = PaginationHelper::show($goods->total(), $goods->currentPage(), $goods->perPage());

        return view('Yunshop\Appletslive::admin.goods_add', [
            'brand' => $brand,
            'goods' => $goods,
            'pager' => $pager,
            'request' => $input,
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

        Goods::refresh();
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

        Goods::refresh();
        Goods::where('id', $id)->update(['reset_audit' => 0, 'audit_id' => $result['auditId']]);
        return $this->message('提审成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
    }

    // 更新商品
    public function edit()
    {
        if (request()->isMethod('post')) {

            $param = request()->all();
            unset($param['c']);
            unset($param['a']);
            unset($param['m']);
            unset($param['do']);
            unset($param['route']);
            $post_data = $param;

            $id = array_key_exists('id', $param) ? intval($param['id']) : 0;
            $info = Goods::where('id', $id)->first();
            if (!$info) {
                return $this->errorJson('无效的商品ID');
            }

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

            // 商品没有任何更改
            if ($post_data['name'] == $info['name'] && $post_data['goodsId'] == $info['goods_id']
                && ($post_data['coverImgUrl'] == $info['cover_img_url'] || $post_data['coverImgUrl'] == $info['cover_img_origin'])
                && $post_data['priceType'] == $info['price_type'] && $post_data['price'] == $info['price']
                && $post_data['price2'] == $info['price2'] && $post_data['goodsId'] == $info['goods_id']) {
                return $this->errorJson('商品没有任何更改');
            }

            // 处理商品图片
            if ($param['coverImgUrl'] == $info['cover_img_url']) {
                if (empty($info['cover_img_origin'])) {
                    return $this->errorJson('原始图片丢失，请重新上传图片');
                } else {
                    $param['coverImgUrl'] = $info['cover_img_origin'];
                }
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
            $post_data['url'] = $this->goodsUrlPrefix . $param['goodsId'];

            // 调用小程序接口添加商品并提审
            unset($post_data['id']);
            $post_data['goodsId'] = $info['id'];
            $result = (new BaseService())->updateGoods($post_data);

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

            $update_data = [
                'goods_id' => $param['goodsId'],
                'name' => $post_data['name'],
                'price_type' => $post_data['priceType'],
                'price' => $post_data['price'],
                'price2' => $post_data['price2'],
                'cover_img_origin' => $param['coverImgUrl'],
                'url' => $post_data['url'],
            ];
            Goods::where('id', $id)->update($update_data);

            Goods::refresh();
            return $this->successJson('商品更新成功');
        }

        $id = request()->get('id', 0);
        $info = Goods::where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的商品ID', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.goods_edit', [
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

        Goods::refresh();
        Goods::where('id', $id)->delete();
        return $this->message('删除成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
    }
}
