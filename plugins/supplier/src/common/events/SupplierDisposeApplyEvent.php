<?php

namespace Yunshop\Supplier\common\events;

use app\common\events\Event;
use Yunshop\Supplier\common\models\Supplier;

class SupplierDisposeApplyEvent extends Event
{

    protected $apply_model;

    protected $type;

    public function __construct(Supplier $apply, $type = 0)
    {
        $this->apply_model = $apply;

        $this->type = $type;

    }
    
    public function getApplyModel()
    {
        return $this->apply_model;
    }

    public function getApplyType()
    {
        return $this->type;
    }
}