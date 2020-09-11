<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-28
 * Time: 12:45
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
namespace Yunshop\Appletslive\common\models;

use app\common\models\BaseModel;
use Yunshop\Appletslive\common\services\BaseService;

class Goods extends BaseModel
{
    public $table = "yz_appletslive_goods";

    public $timestamps = false;

    // 同步商品列表(与小程序官方后台同步数据)
    public static function refresh()
    {
        $service = new BaseService();
        $present = [];
        for ($i = 0; $i < 4; $i++) {
            $temp = $service->getGoods($i);
            if (is_array($temp) && $temp['errcode'] == 0 && !empty($temp['goods'])) {
                array_walk($temp['goods'], function (&$item) use ($i) {
                    $item['yzGoodsId'] = 0;
                    if (preg_match('/goods_id=([0-9]+)$/', $item['url'])) {
                        $url_info = explode('=', $item['url']);
                        $item['yzGoodsId'] = $url_info[count($url_info) - 1];
                    }
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
        $stored = self::orderBy('id', 'desc')->limit(100)->get();

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
                        || $stv['audit_status'] != $spv['auditStatus'] || $stv['reset_audit'] != $reset_audit
                        || $stv['goods_id'] != $spv['yzGoodsId']) {
                        array_push($update, [
                            'id' => $stv['id'],
                            'goods_id' => $spv['yzGoodsId'],
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
                    'goods_id' => $spv['yzGoodsId'],
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
            self::insert($insert);
        }
        if ($update) {
            foreach ($update as $item) {
                $id = $item['id'];
                $temp = $item;
                unset($temp['id']);
                self::where('id', $id)->update($temp);
            }
        }
        if ($delete) {
            self::whereIn('id', $delete)->delete();
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
