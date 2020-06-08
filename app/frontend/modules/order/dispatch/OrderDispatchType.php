<?php


namespace app\frontend\modules\order\dispatch;


use app\frontend\modules\dispatch\models\PreOrderAddress;
use app\frontend\modules\order\models\PreOrder;

abstract class OrderDispatchType
{
    //protected $order;
    protected $dispatchTypeSetting;

    public function __construct($dispatchTypeSetting = null)
    {
        //$this->order = $order;
        $this->dispatchTypeSetting = $dispatchTypeSetting;
    }

    public function getId()
    {
        return $this->dispatchTypeSetting['id'];
    }

    public function getCode()
    {
        return $this->dispatchTypeSetting['code'];
    }


    public function enable()
    {
        return $this->dispatchTypeSetting['enable'];
    }

    public function sort()
    {
        return $this->dispatchTypeSetting['sort'] + $this->getId() / 100;
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            'dispatch_type_id' => $this->dispatchTypeSetting['id'],
            'name' => $this->dispatchTypeSetting['name'],
        ];
    }

    public function preOrderAddress()
    {
        return new PreOrderAddress();
    }
}