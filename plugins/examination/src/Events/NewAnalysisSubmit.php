<?php

namespace Yunshop\Examination\Events;

use app\common\events\Event;
use Yunshop\FaceAnalysis\models\FaceAnalysisLogModel;

class NewAnalysisSubmit extends Event
{
    public $serviceId;
    public $memberId;
    public $label;

    public function __construct(int $serviceId, int $memberId, int $label)
    {
        $this->serviceId = $serviceId;
        $this->memberId = $memberId;
        $this->label = $label;
    }
}
