<?php

namespace Yunshop\Supplier\common\events;

use app\common\events\Event;
use Yunshop\Supplier\common\models\Supplier;

class SupplierApplyEvent extends Event
{

    protected $apply_model;

    public function __construct(Supplier $apply)
    {
        $this->apply_model = $apply;

    }
    
    public function getApplyModel()
    {
        return $this->apply_model;
    }
}