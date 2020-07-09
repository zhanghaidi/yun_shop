<?php

namespace Yunshop\Diyform\models;

use app\common\modules\orderGoods\models\PreOrderGoods;

class PreOrderGoodsDiyForm extends OrderGoodsDiyForm
{
    /**
     * @var PreOrderGoods
     */
    private $orderGoods;
    /**
     * @var DiyformOrderModel
     */
    private $diyForm;

    public function setOrderGoods(PreOrderGoods $orderGoods)
    {

        $this->orderGoods = $orderGoods;
        $this->uniacid = $orderGoods->uniacid;
        $this->form_id = $this->diyForm() ? $this->diyForm()['form_id'] : null;

        $this->diyform_data_id = $orderGoods->getParams('diyform_data_id') ?: 0;
    }

    public function enable()
    {
        if (!$this->diyForm()) {
            return false;
        }
        if (!$this->diyForm()->status) {
            return false;
        }
        return true;
    }

    private function diyForm()
    {
        if (!isset($this->diyForm)) {
            $this->diyForm = $model = DiyformOrderModel::where([
                'goods_id' => $this->orderGoods->goods_id
            ])->first() ?: [];
        }
        return $this->diyForm;
    }
}