<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/8
 * Time: 上午11:41
 */

namespace Yunshop\Mryt\store\common\models;


use app\common\services\Session;
use Yunshop\Mryt\store\models\Store;
use Yunshop\Mryt\store\models\StoreGoods;

class Goods extends \app\backend\modules\goods\models\Goods
{
    protected $appends = ['store_name', 'store_id'];
    static protected $needLog = false;

    public function getStoreNameAttribute()
    {
        if ($this->hasOneStoreGoods && $this->hasOneStoreGoods->hasOneStore) {
            return $this->hasOneStoreGoods->hasOneStore->store_name;
        }
    }

    public function getStoreIdAttribute()
    {
        if ($this->hasOneStoreGoods) {
            return $this->hasOneStoreGoods->store_id;
        }
    }

    public function scopePluginId($query, $pluginId = Store::PLUGIN_ID)
    {
        return $query->where('plugin_id', $pluginId);
    }

    public function scopeIsPlugin($query)
    {
        return $query;
    }

    public static function getGoodsList($search, $storeId = 0)
    {
        return self::select()->search($search)->hasOneStoreGoods($storeId);
    }

    public function scopeHasOneStoreGoods($query, $storeId)
    {
        return $query->whereHas('hasOneStoreGoods', function($storeGoods)use($storeId){
                $storeGoods->whereIn('store_id',$storeId);
        });
    }

    public function hasOneStoreGoods()
    {
        return $this->hasOne(StoreGoods::class, 'goods_id', 'id');
    }

    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }

        $saleModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $saleModel->delete();
        } else if ($operate == 'created') {
            $storeGoodsData = [
                'goods_id' => $goodsId,
                'store_id' => Session::get('store_cashier_store')['id']
            ];

            $saleModel->fill($storeGoodsData);
            $res = $saleModel->save();
            if ($res) {
                $goodsModel = \app\common\models\Goods::find($goodsId);
                $goodsModel->plugin_id = Store::PLUGIN_ID;
                $goodsModel->save();
            }
            return $saleModel->save();
        }
    }

    public static function getModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = StoreGoods::getModelByGoodsIdAndByStoreId($goodsId, Session::get('store_cashier_store')['id']);
        }
        !$model && $model = new StoreGoods();

        return $model;
    }

    /**
     * 获取商品名称
     * @return html
     */
    public static function getSearchOrder()
    {
        $keyword = \YunShop::request()->keyword;
        $keyword = trim($keyword);
        return Goods::select(['id','title', 'thumb', 'plugin_id'])->pluginId()->where('title', 'like', '%'.$keyword.'%')->orWhere('id',$keyword)->get();
    }
}