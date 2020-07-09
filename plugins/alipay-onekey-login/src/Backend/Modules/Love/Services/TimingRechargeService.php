<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/26
 * Time: 3:54 PM
 */

namespace Yunshop\Love\Backend\Modules\Love\Services;


use app\common\traits\MessageTrait;
use Yunshop\Love\Backend\Modules\Love\Models\TimingLogModel;
use Yunshop\Love\Common\Models\LoveTimingQueueModel;

class TimingRechargeService
{
    use MessageTrait;

    /**
     * @var array
     */
    private $timing_rule;

    /**
     * @var LoveTimingQueueModel
     */
    private $timingLogModel;



    public function addTimingQueue(TimingLogModel $timingLogModel)
    {
        $this->timingLogModel = $timingLogModel;
        $timing_rules = $this->timingLogModel->timing_rule;

        foreach ($timing_rules as $key => $timing_rule) {
            $this->timing_rule = $timing_rule;
            $result = $this->_addTimingQueue();
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    private function _addTimingQueue()
    {
        $model = new LoveTimingQueueModel();

        $model->fill($this->getTimingQueueData());

        $validator = $model->validator();
        if ($validator->fails()) {
            $this->error($validator->messages());
            return false;
        }

        return $model->save();
    }

    /**
     * @return array
     */
    private function getTimingQueueData()
    {
        return [
            'uniacid'       => $this->timingLogModel->uniacid,
            'member_id'     => $this->timingLogModel->member_id,
            'change_value'  => $this->timingLogModel->amount,
            'timing_days'   => $this->timing_rule['timing_days'],
            'timing_rate'   => $this->timing_rule['timing_rate'],
            'status'        => 0,
            'recharge_sn'   => $this->timingLogModel->recharge_sn,
        ];
    }

}
