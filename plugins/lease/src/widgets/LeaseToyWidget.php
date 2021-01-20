<?php

namespace Yunshop\LeaseToy\widgets;

use app\common\components\Widget;
use Yunshop\LeaseToy\models\LeaseToyGoodsModel;
use Yunshop\LeaseToy\models\LeaseOrderModel;

/**
* 
*/
class LeaseToyWidget extends Widget
{
    
    public function run()
    {
        $model = LeaseToyGoodsModel::ofGoodsId($this->goods_id)->first();
        $model = $model ? $model->toArray() : $this->getDefaultData();
        return view('Yunshop\LeaseToy::admin.lease-toy-goods', [
            'lease_toy' => $model,
        ])->render();
    }

    /**
     *验证数据
     * @param  [type] $goodsId [description]
     * @param  [type] $data    [description]
     * @param  [type] $operate [description]
     * @return [type]          [description]
     */
    public function relationValidator($goodsId, $data, $operate)
    {
        $flag = false;
        $model = new LeaseToyGoodsModel;
        $model->fill($data);
        $validator = $model->validator($data);
        if ($validator->fails()) {
            $model->error($validator->messages());
        } else {
            $flag = true;
        }
        return $flag;
    }

    public function relationSave($goodsId, $data, $operate)
    {
        if(!$goodsId){
            return false;
        }

        $LeaseToyGoods = false;
        if ($operate != 'created') {
            $LeaseToyGoods = LeaseToyGoodsModel::ofGoodsId($goodsId)->first();
        }
        !$LeaseToyGoods && $LeaseToyGoods = new LeaseToyGoodsModel();

        if ($operate == 'deleted') {
            return $LeaseToyGoods->delete();
        }
        $data['goods_id'] = $goodsId;
        $data['uniacid'] = \YunShop::app()->uniacid;
        $LeaseToyGoods->fill($data);
        if ($LeaseToyGoods->save()) {
        	$goods = \app\common\models\Goods::find($goodsId);
	    	if ($LeaseToyGoods->is_lease && $goods->plugin_id != LeaseOrderModel::PLUGIN_ID) {
	    		$goods->plugin_id =  LeaseOrderModel::PLUGIN_ID;
	    		$goods->save();
	    	}
	    	if ((!$LeaseToyGoods->is_lease) && $goods->plugin_id == LeaseOrderModel::PLUGIN_ID) {
	            $goods->plugin_id =  0;
	            $goods->save();
	        }
        	return true;
        }
        return false;
    }

    /**
     * 获取默认商品数据，当这个商品没有租赁设置时
     * @return array
     */
    private function getDefaultData()
    {
        return [
            'is_rights' => '1',
            'is_lease' => '0',
            'goods_deposit' => '0',
            'immed_goods_id' => '0',
        ];
    }
   
}