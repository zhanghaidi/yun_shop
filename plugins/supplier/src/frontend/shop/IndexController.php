<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 15:36
 */

namespace Yunshop\Supplier\frontend\shop;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\components\ApiController;
use app\common\models\Setting;
use app\frontend\modules\member\controllers\ServiceController;
use Yunshop\Room\models\Room;
use Yunshop\Supplier\supplier\models\Adv;
use Yunshop\Supplier\supplier\models\Slide;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\models\SupplierGoods;
use app\common\models\Goods;

class IndexController extends ApiController
{
    public function index()
    {
        $sid = intval(request()->sid);
        $supplier = Supplier::getSupplierById($sid);
        $supplier_uid = $supplier->uid;
        $slide = Slide::getSlides()->whereHas('hasOneSupplier', function ($query) use($supplier_uid) {
            $query->where('uid', $supplier_uid);
        })->get();

        $slide->map(function ($slide) {
            if (strexists($slide->thumb, 'image/')) {
                $slide->thumb = replace_yunshop(yz_tomedia($slide->thumb, 'image'));
            }else{
                $slide->thumb = replace_yunshop(yz_tomedia($slide->thumb));
            }
        });

        $advs_array = [];
        $advs = Adv::whereHas('hasOneSupplier', function ($query) use ($supplier_uid) {
            $query->where('uid', $supplier_uid);
        })->first();
        $advs_data = $advs->advs;

        foreach ($advs_data as $item) {
            if (strexists($item['img'], 'image/')) {
                $item['img'] = replace_yunshop(yz_tomedia($item['img'], 'image'));
                $advs_array[] = $item;
            }else{
                $item['img'] = replace_yunshop(yz_tomedia($item['img']));
                $advs_array[] = $item;
            }
        }

        if ($sid == 0) {
            throw new ShopException('供应商SID错误');
        }

        if (!$supplier) {
            throw new ShopException('此SID不是供应商');
        }
        $supplier->logo = replace_yunshop(yz_tomedia($supplier->logo));
        if ($supplier->store_name == 'null') {
            $supplier->store_name = $supplier->username;
        }
        $goods_ids = SupplierGoods::getGoodsIdsBySid($sid);
        if ($goods_ids->isEmpty()) {
            throw new ShopException('此供应商没有商品');
        }
        $list_count = Goods::select('*', 'yz_goods.id as goods_id')
            ->whereIn("id", $goods_ids)
            ->where('status', 1)
            ->where('plugin_id',92)
            ->count();
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        $list_is_recommand = $goods_model->select('*', 'yz_goods.id as goods_id')
            ->whereIn("id", $goods_ids)
            ->where('is_recommand', 1)
            ->where('status', 1)
            ->where('plugin_id',92)
            ->orderBy('id', 'desc')
            ->limit(4)
            ->get();
        $list_is_recommand->vip_level_status;

        $list_is_hot = $goods_model->select('*', 'yz_goods.id as goods_id')
            ->whereIn("id", $goods_ids)
            ->where('is_hot', 1)
            ->where('status', 1)
            ->where('plugin_id',92)
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();
        $list_is_hot->vip_level_status;

        $list_is_recommand->map(function ($list_is_recommand) {
            if (strexists($list_is_recommand->thumb, 'image/')) {
                $list_is_recommand->thumb = replace_yunshop(yz_tomedia($list_is_recommand->thumb,'image'));
            } else {
                $list_is_recommand->thumb = replace_yunshop(yz_tomedia($list_is_recommand->thumb));
            }
        });

        $list_is_hot->map(function ($list_is_hot) {
            if (strexists($list_is_hot->thumb, 'image/')) {
                $list_is_hot->thumb = replace_yunshop(yz_tomedia($list_is_hot->thumb,'image'));
            } else {
                $list_is_hot->thumb = replace_yunshop(yz_tomedia($list_is_hot->thumb));
            }
        });

        //客服链接
        $supplier_cservice = \Setting::get('plugin.supplier.meiqia[' . $supplier->uid . ']');
        $cservice = \Setting::get('shop.shop.cservice');
        if (!empty($supplier_cservice['meiqia'])) {
            $supplier_link = $supplier_cservice['meiqia'];
        }else{
            $supplier_link = $cservice;
        }
        $res = [
            'list_is_hot' => $list_is_hot,
            'list_is_recommand' => $list_is_recommand,
            'list_count' => $list_count,
            'supplier' => $supplier,
            'slide' => $slide,
            'advs' => $advs_array,
            'supplier_link' => $supplier_link,
            'is_room' =>  (integer)(app('plugins')->isEnabled('room') && \Setting::get('plugin.room_set_basic')['is_open_room'])
        ];
        $customer_service = (new ServiceController())->supplier_set($supplier->uid,request()->type);
        if($customer_service['mark'])
        {
            unset($customer_service['mark']);
            foreach ($customer_service as $k => $v){
                $res[$k] = $v;
            }
        }
        return $this->successJson('成功',$res);
    }

    public function searchGoods()
    {
        $sid = intval(request()->sid);
        if ($sid == 0) {
            throw new ShopException('供应商SID错误');
        }
        $goods_ids = SupplierGoods::getGoodsIdsBySid($sid);

        $requestSearch = \YunShop::request()->search;

        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['id', 'price', 'show_sales'])) {
            $order_field = 'display_order';
        }

        $order_by = (\YunShop::request()->order_by == 'asc')? 'asc' : 'desc';

        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item) && $item !== 0 && $item !== 'undefined';
            });
        }

        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        $list = $goods_model->Search($requestSearch)->select('*', 'yz_goods.id as goods_id')
            ->whereIn('id', $goods_ids)
            ->where('status', 1)
            ->where('plugin_id',92)
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();

        if ($list['total'] > 0) {
            $data = collect($list['data'])->map(function ($rows) {
                return collect($rows)->map(function ($item, $key) {
                    if ($key == 'thumb') {
                        return replace_yunshop(yz_tomedia($item));
                    }else{
                        return $item;
                    }
                });
            })->toArray();

            $list['data'] = $data;
        }

        if (empty($list['data'])) {
            return $this->errorJson('没有找到商品!');
        }else{
            return $this->successJson('成功', $list);
        }
    }

    public function goodsLimit()
    {
        $sid = intval(request()->sid);
        if ($sid == 0) {
            throw new ShopException('供应商SID错误');
        }
        $supplier = Supplier::getSupplierById($sid);
        if (!$supplier) {
            throw new ShopException('此SID不是供应商');
        }
        $supplier->logo = replace_yunshop(yz_tomedia($supplier->logo));
        $goods_ids = SupplierGoods::getGoodsIdsBySid($sid);
        if ($goods_ids->isEmpty()) {
            throw new ShopException('此供应商没有商品');
        }
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        $list = $goods_model->select('*', 'yz_goods.id as goods_id')
            ->whereIn("id", $goods_ids)
            ->where('status', 1)
            ->where('plugin_id',92)
            ->whereHas('hasOneGoodsLimitBuy')
            ->with('hasOneGoodsLimitBuy')
            ->orderBy('id', 'desc')
            ->paginate(20);
        $list->vip_level_status;
        $list = $list->toArray();

        if ($list['total'] > 0) {
            $data = collect($list['data'])->map(function ($rows) {
                return collect($rows)->map(function ($item, $key) {
                    if ($key == 'thumb') {
                        return replace_yunshop(yz_tomedia($item));
                    } else {
                        return $item;
                    }
                });
            })->toArray();

            $list['data'] = $data;
        }

        if (is_null($list['data'])) {
            throw new ShopException('没有找到商品');
        }
        return $this->successJson('成功', [
            'list' => $list,
        ]);
    }

    public function getRoom()
    {
        $supplier_id = intval(request()->supplier_id);
        $rooms = Room::select('yz_room.*','yz_room_record_file.id as back_id')
            ->where('yz_room.role_id',2)
            ->where('yz_room.shop_id',$supplier_id)
            ->where(function ($querys) {
                $querys->whereIn('status',[2,3])->orwhere(function ($query) {
                    $query->where('status',4)
                        ->where('yz_room_record_file.id','>',0)
                        ->where('yz_room_record_file.is_show',1);
                });
            })
            ->leftJoin('yz_room_record_file',function ($join) {
                $join->on('yz_room_record_file.room_id','=','yz_room.id');
            })
            ->with('hasOneMember')
            ->orderByRaw("FIELD(status, " . implode(", ", [3,2,4]) . ")")
            ->orderBy('yz_room.recommend','asc')
            ->orderBy('yz_room_record_file.recommend','asc')
            ->orderBy('yz_room.id','desc')
            ->paginate(10);
        $room = [];
        foreach ($rooms as $key=>$val) {
            $room[$key]['avatar'] = $val->hasOneMember['avatar_image'];
            $room[$key]['nickname'] = $val->hasOneMember['nickname'];
            $room[$key]['id'] = $val->id;
            $room[$key]['status'] = $val->status;
            $room[$key]['title'] = $val->title;
            $room[$key]['cover'] = yz_tomedia($val->cover);
            $room[$key]['banner'] = yz_tomedia($val->banner);
            $room[$key]['live_time'] = $val->live_time;
            $room[$key]['view_num'] = $val->view_count + $val->virtual;
            if (empty($val->goods)) {
                $room[$key]['goods_num'] = 0;
            } else {
                $room[$key]['goods_num'] = count(explode(',', $val->goods));
            }
            if ($val->status == 2) {
                $room[$key]['play_type'] = 3;
            } elseif ($val->status == 3) {
                $room[$key]['play_type'] = 1;
            } else{
                $room[$key]['play_type'] = 2;
                $room[$key]['back_id'] = $val->back_id;
            }
        }
        $json = $rooms->toArray();
        $json['data'] = $room;
        return $this->successJson('成功', $json);
    }
}
