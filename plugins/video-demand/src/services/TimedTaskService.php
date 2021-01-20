<?php
namespace Yunshop\VideoDemand\services;
use app\common\facades\Setting;
use app\common\models\Income;
use app\common\models\Member;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\Log;
use Yunshop\VideoDemand\models\CourseGoodsModel;
use Yunshop\VideoDemand\models\LecturerModel;
use Yunshop\VideoDemand\models\LecturerRewardLogModel;


class TimedTaskService
{
    public function handle()
    {
        $uniAccount = UniAccount::get();
        Log::info('课程点播-分红结算');
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;

            Setting::$uniqueAccountId = $u->uniacid;

            $request = LecturerRewardLogModel::getStatement()->get();

            if($request->toArray()){
                $this->setStatement($request);
            }
        }
    }

    public function setStatement($lecturerReward)
    {
        foreach ($lecturerReward as $item)
        {
//            //修改状态-已结算
           $this->updatedStatement($item);
//            //加入收入
           $this->addLecturerRewardIncome($item);
            // 发送消息
            $this->notice($item);
        }
    }

    /**
     * @param $data
     */
    public function notice($data)
    {
        $lecturer = LecturerModel::find($data['lecturer_id']);
        $member = Member::getMemberByUid($lecturer['member_id'])->with('hasOneFans')->first();
        $course = CourseGoodsModel::find($data['course_id']);
        $messageData = [
            'goods_name' => $course->goods_title,
            'order_price' => $data['order_price'],
            'amount' => $data['amount'],
        ];

        MessageService::orderRewardSettle($messageData, $member->hasOneFans);
    }


    /**
     * @param $rewardData
     * @return mixed
     */
    public function updatedStatement($rewardData)
    {
        return LecturerRewardLogModel::uniacid()->where('id',$rewardData['id'])->update(['status'=>'1','updated_at'=>time()]);
    }


    public function addLecturerRewardIncome($rewardData)
    {
        //收入明细数据
        $config = \Config::get('income.videoDemand');
        $lecturerData = LecturerModel::find($rewardData['lecturer_id']);
        //收入数据
        $incomeData = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $lecturerData->member_id,
            'incometable_type' => $config['class'],
            'incometable_id' => $rewardData->id,
            'type_name' => $config['title'],
            'amount' => $rewardData->amount,
            'status' => '0',
            'detail' => '',
            'create_month' => date("Y-m"),
        ];
        //插入收入
        $incomeModel = new Income();
        $incomeModel->setRawAttributes($incomeData);
        $requestIncome = $incomeModel->save();
        if ($requestIncome) {
            \Log::info(time() . ":收入统计插入数据-视频点播!");
        }
        return $requestIncome;
    }
}