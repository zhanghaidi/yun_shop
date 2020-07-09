<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/26
 * Time: 15:06
 */

namespace Yunshop\Diyform\widgets;

use app\common\components\Widget;
use app\common\models\Order;
use app\common\models\OrderGoods;
use Yunshop\Diyform\models\DiyformDataModel;
use Yunshop\Diyform\models\DiyformTypeModel;
use Yunshop\Diyform\models\OrderGoodsDiyForm;
use Yunshop\PackageDeliver\model\PackageDeliverOrder;

class DiyFormOrderWidget extends Widget
{
    public function run()
    {
        $orderGoodsIds = OrderGoods::where('order_id', $this->order_id)->pluck('id');

        $dataIds = OrderGoodsDiyForm::whereIn('order_goods_id', $orderGoodsIds)->pluck('diyform_data_id');
        $datas = DiyformDataModel::whereIn('id', $dataIds)->get()->toArray();

        $diyformItems = [];
        foreach ($datas as $detail) {
            if ($detail) {
                $form = DiyformTypeModel::find($detail['form_id']);
            }

            $fields = iunserializer($form['fields']);
            $diyformItems[] = [
                'fields'=>$fields,
                'detail'=>$detail,
            ];
        }

        return view('Yunshop\Diyform::widget.order', [
            'diyform_items' => $diyformItems,
        ])->render();
    }

    public static function getDiyFormData($order_id)
    {
        $orderGoodsIds = OrderGoods::where('order_id', $order_id)->pluck('id');

        $dataIds = OrderGoodsDiyForm::whereIn('order_goods_id', $orderGoodsIds)->pluck('diyform_data_id');
        $datas = DiyformDataModel::whereIn('id', $dataIds)->get()->toArray();
        foreach ($datas as &$data) {
            $data['data'] = iunserializer($data['data']);
        }

        return $datas;
    }
}