<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/6
 * Time: 11:33
 */

namespace Yunshop\Designer\services;

use Yunshop\Designer\models\GoodsGroupGoods;

class GoodsGroupGoodsService
{
    public function FunGroupGoods($datas, $type = "", $fun = '', $id = '')
    {//循环出所有的商品组
        $datas = json_decode(htmlspecialchars_decode($datas), true);
        $goods_group = new GoodsGroupGoods();
        if (is_array($datas)) {//判断是否数组格式
            $group_count = collect($datas)->count();//获取商品组的个数
            $count = 0;
            foreach ($datas as $data) {//循环商品组
                ++$count;
                if ($data['temp'] == 'goods' || $data['temp'] == 'flashsale') {//判断是否是商品组
                    if ($group_count == $count) {//给商品组最后一个加上标识符
                        $data['Identification'] = 1;
                    } else {
                        $data['Identification'] = 0;
                    }
                    switch ($fun) {
                        case 'update':
                            $result = $goods_group->GetGroupGoodsId($data['id']);

                            if ($result->count()) {//判断商品组是否有商品
                                if ($goods_group->DelGroupGoods($data['id'])) {//修改功能先删除后更新
                                    $this->SaveGroupGoods($data, $type, $id);
                                } else {
                                    throw new AppException("修改失败");
                                }
                            } else {//如果商品组没有商品则直接添加数据
                                $this->SaveGroupGoods($data, $type, $id);
                            }

                            break;
                        case 'delete':
                            $result = $goods_group->GetGroupGoodsId($data['id']);
                            if (!isset($result)) {//判断商品组是否有商品
                                if (!$goods_group->DelGroupGoods($data['id'])) {//修改功能先删除后更新
                                    throw new AppException("删除失败");
                                }
                            }
                            break;
                        case 'insert':
                            $this->SaveGroupGoods($data, $type, $id);
                            break;
                        case 'select_page'://进入首页默认显示二十条数据
                            return $goods_group->GetGroupGoods($data['id']);//获取二十条数据
                            break;

                    }
                }
            }
        }
    }

    public function SaveGroupGoods($data, $type, $id)
    {//保存商品组所有商品
        $datas = collect($data['data']);
        foreach ($datas as $item) {//循环商品组里的商品
            $goods_group = new GoodsGroupGoods();
            $goods_group->group_goods_id = $item['id'];
            $goods_group->uniacid = \Yunshop::app()->uniacid;
            $goods_group->group_id = $data['id'];
            $goods_group->goods_id = $item['goodid'];
            $goods_group->goods = serialize($item);
            $goods_group->group_type = $type;
            $goods_group->Identification = $data['Identification'];
            $goods_group->temp = $data['temp'];
            $goods_group->page_id = $id;
            if (!$goods_group->save()) {
                throw new AppException("商品添加商品组失败");
            }

        }
    }
}