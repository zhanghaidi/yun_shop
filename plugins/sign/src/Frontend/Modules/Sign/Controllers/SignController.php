<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/8 上午11:41
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Frontend\Modules\Sign\Controllers;


use app\common\components\ApiController;
use app\common\helpers\Cache;
use app\common\models\notice\MessageTemp;
use app\common\services\finance\PointService;
use app\common\services\MessageService;
use app\frontend\modules\coupon\services\CouponSendService;
use app\Jobs\SendTemplateMsgJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Sign\Common\Services\SetService;
use Yunshop\Sign\Frontend\Models\SignLog;
use Yunshop\Sign\Frontend\Services\SignAwardService;

class SignController extends ApiController
{

    /**
     * @var SignAwardService
     */
    private $awardService;

    public $award_love;

    public function __construct()
    {
        parent::__construct();

        $this->awardService = new SignAwardService();
        $this->award_love = $this->getRand();
    }

    /**
     * 签到接口
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function sign()
    {
        $member_id = \YunShop::app()->getMemberId();
        if (!$member_id) {
            return $this->errorJson('会员未登陆');
        }
        if (!SetService::getSignSet('sign_status')) {
            return $this->errorJson('未开启签到功能');
        }
        if ($this->awardService->signModel->sign_status) {
            return $this->errorJson('今日已经'.trans('Yunshop\Sign::sign.plugin_name'));
        }

        $result = $this->signStart();
        if (!$result) {
            return $this->errorJson(('Yunshop\Sign::sign.plugin_name').'失败，请重试！');
        }
        /**
         * 清除首页店铺装数据缓存
         */
        Cache::flush();
        return $this->successJson(trans('Yunshop\Sign::sign.plugin_name').'成功',['success_url' => $this->getSuccessLink()]);

    }


    private function signStart()
    {
        DB::beginTransaction();


        $singLog = new SignLog();

        $data = $this->getSignLogData();

        $singLog->fill($data);
        $validator = $singLog->validator();

        if ($validator->fails()) {
            return false;
        }

        if (!$singLog->save()) {
            return false;
        }

        /**
         * 奖励计算需要在更新签到状态前计算，否则会导致联系签到天数计算值 多+1
         */
        $result = $this->sendSignAward();
        if (!$result) {
            DB::rollBack();
            return false;
        }

        $result = $this->updateSign();
        if (!$result) {
            DB::rollBack();
            return false;
        }

        $singLog->status = 1;
        if (!$singLog->save()) {
            DB::rollBack();
            return false;
        }


        DB::commit();

        $this->notice();
        return true;
    }



    private function updateSign()
    {
        $this->awardService->signModel->uniacid = \YunShop::app()->uniacid;
        $this->awardService->signModel->member_id = \YunShop::app()->getMemberId();
        $this->awardService->signModel->cumulative_point = $this->awardService->signModel->cumulative_point + $this->awardService->getAwardPoint();
        $this->awardService->signModel->cumulative_coupon = $this->awardService->signModel->cumulative_coupon + $this->awardService->getAwardCouponNum();
        $this->awardService->signModel->cumulative_number = $this->awardService->getCumulativeNumber();
        $this->awardService->signModel->cumulative_love = $this->awardService->signModel->cumulative_love + $this->award_love;

        return $this->awardService->signModel->save();
    }


    private function sendSignAward()
    {
        $result = $this->awardMemberPoint();
        if (!$result) {
            return false;
        }
        $result = $this->awardMemberCoupon();
        if (!$result) {
            return false;
        }
        //如果开启爱心值插件，奖励会员爱心值
        if (app('plugins')->isEnabled('love')) {
            $result = $this->awardMemberLove();
            if (!$result) {
                return false;
            }
        }

        return true;
    }


    private function awardMemberPoint()
    {
        $change_value = $this->awardService->getAwardPoint();

        if ($change_value <= 0) {
            return true;
        }
        $data = [
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode'        => PointService::POINT_MODE_SIGN_REWARD,
            'member_id'         => \YunShop::app()->getMemberId(),
            'point'             => $change_value,
            'remark'            => trans('Yunshop\Sign::sign.plugin_name') . '奖励积分：'.$change_value. '积分' ,
        ];

        $result = (new PointService($data))->changePoint();
        if (!$result) {
            Log::info('签到奖励积分', $data ?: []);
        }
        return true;
    }


    private function awardMemberCoupon()
    {
        $coupons = $this->awardService->getAwardCoupon();
//dd($coupons);
        $coupon_ids = [];
        foreach ($coupons as $key => $item) {

            if ($item['coupon_num'] > 1) {
                for ($i=0; $i < $item['coupon_num']; $i++) {
                    $coupon_ids[] = $item['coupon_id'];
                }

            } else {
                $coupon_ids[] = $item['coupon_id'];
            }
        }

        if (count($coupon_ids) > 0) {
            return (new CouponSendService())->sendCouponsToMember(\YunShop::app()->getMemberId(), $coupon_ids,6);
        }

        return true;
    }

    private function awardMemberLove()
    {
        $change_value = $this->award_love;

        if ($change_value <= 0) {
            return true;
        }

        $data = [
            'member_id' => \YunShop::app()->getMemberId(),
            'change_value' => $change_value,
            'operator' => \app\common\services\credit\ConstService::OPERATOR_MEMBER,
            'operator_id' => \YunShop::app()->getMemberId(),
            'remark' => '签到奖励爱心值' . $change_value,
            'relation' => ''
        ];
        
        $love_set = \Yunshop\Love\Common\Services\SetService::getLoveSet();
        $result = (new LoveChangeService($love_set['award_type']))->signAward($data);
        if (!$result) {
            Log::info('签到奖励爱心值', $data ?: []);
        }

        return true;
    }

    private function getSignLogData()
    {
        return [
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => \YunShop::app()->getMemberId(),
            'award_point'   => $this->awardService->getAwardPoint(),
            'award_coupon'  => $this->awardService->getAwardCouponNum(),
            'award_love'    => $this->award_love?:0,
            'status'        => 0,
            'remark'        => $this->getSignLogRemark()
        ];
    }





    private function getSignLogRemark()
    {
        $every_point = $this->awardService->getEveryAwardPoint();
        $every_coupon = $this->awardService->getEveryAwardCoupon()[0]['coupon_num'] ?: 0;
        $every_love = $this->award_love?:0;

        $cumulative_point = $this->awardService->getCumulativeAwardPoint();
        $cumulative_coupon= $this->awardService->getCumulativeAwardCoupon();

        $coupon_num = 0;
        foreach ($cumulative_coupon as $key => $item) {
            $coupon_num += $item['coupon_num'];
        }

        if (app('plugins')->isEnabled('love')) {
            $remark = '每日奖励：' . $every_point . "积分，优惠券：(" . $every_coupon . ")张;" . $every_love . \Yunshop\Love\Common\Services\SetService::getLoveName()."；";

            $remark = $remark . '连签奖励：' . $cumulative_point . "积分，优惠券：(" . $coupon_num . ")张;";
        }else{
            $remark = '每日奖励：' . $every_point . "积分，优惠券：(" . $every_coupon . ")张;" ;

            $remark = $remark . '连签奖励：' . $cumulative_point . "积分，优惠券：(" . $coupon_num . ")张;";
        }

        return $remark;
    }




    private function getSuccessLink()
    {
        $success_link = SetService::getSignSet('success_link');

        return $success_link ?: yzAppFullUrl('member/sign');
    }



    private function notice()
    {
        $temp_id = SetService::getSignSet('sign_notice');
        if (!$temp_id) {
            return;
        }
        $params = $this->getNoticeContent();
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        MessageService::notice(MessageTemp::$template_id, $msg, $this->awardService->signModel->member_id);
    }


    private function getNoticeContent()
    {
        $params = [
            ['name' => '昵称', 'value' => $this->awardService->signModel->member->realname ?: $this->awardService->signModel->member->nickname],
            ['name' => '签到时间', 'value' => date('Y-m-d H:i:s', time())],
            ['name' => '连签天数', 'value' => $this->awardService->signModel->cumulative_number],
            ['name' => '签到奖励', 'value' => $this->getSignLogRemark()],
        ];
        return $params;
    }

    public function getRand()
    {
        $award_love_min = \Setting::get('sign.award_love_min');
        $award_love_max = \Setting::get('sign.award_love_max');
        $love_rand = mt_rand($award_love_min, $award_love_max); //爱心值随机奖励
        return $love_rand;
    }

    public function testSignReminder()
    {
        $time_now = strtotime(date('Y-m-d', time()));
        $betweenDaySign = 3;
        $startTimes = strtotime(date('Y-m-d', strtotime("-$betweenDaySign day")));
        $whereBetweenSign = [$startTimes, $time_now];

        $sign_users = DB::table('yz_sign')
            ->select('id', 'uniacid', 'member_id', 'updated_at')
            ->whereBetween('updated_at', $whereBetweenSign)
            ->get()->toArray();

        if (!empty($sign_users)) {
            // 2、查询签到用户的小程序用户信息(openid)
            $member_ids = array_unique(array_column($sign_users, 'member_id'));
            $wxapp_user = DB::table('diagnostic_service_user')
                ->select('ajy_uid', 'unionid', 'openid','nickname')
                ->whereIn('ajy_uid', $member_ids)
                ->get()->toArray();
            //如果小程序 公众号ID
            $subscribed_unionid = array_column($wxapp_user, 'unionid');
            $wechat_user = DB::table('mc_mapping_fans')
                ->select('uid', 'unionid', 'openid','nickname')
                ->where('follow', 1)
                ->where('uniacid', 39)
                ->whereIn('unionid', $subscribed_unionid)
                ->get()->toArray();
            //3 组装数据
            array_walk($sign_users, function (&$item) use ($wxapp_user, $wechat_user) {
                //小程序openid
                foreach ($wxapp_user as $user) {
                    if ($user['ajy_uid'] == $item['member_id']) {
                        $item['unionid'] = $user['unionid'];
                        $item['wxapp_openid'] = $user['openid'];
                        $item['wxapp_nickname'] = $user['nickname'];
                        break;
                    }
                }
                //公众号openid
                $item['wechat_openid'] = '';
                foreach ($wechat_user as $user) {
                    if ($user['unionid'] == $item['unionid']) {
                        $item['wechat_openid'] = $user['openid'];
                        $item['wechat_nickname'] = $user['nickname'];
                        break;
                    }
                }
            });

            // 4、组装队列数据
            $job_list = [];
            foreach ($sign_users as $user) {
                $type = ($user['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                $openid = ($user['wechat_openid'] != '') ? $user['wechat_openid'] : $user['wxapp_openid'];

                $job_param = $this->makeJobParam($type, $user);
                array_push($job_list, [
                    'type' => $type,
                    'openid' => $openid,
                    'options' => $job_param['options'],
                    'template_id' => $job_param['template_id'],
                    'notice_data' => $job_param['notice_data'],
                    'page' => $job_param['page'],
                ]);

            }

            // 5、添加消息发送任务到消息队列
            foreach ($job_list as $job_item) {
                $job = new SendTemplateMsgJob($job_item['type'], $job_item['options'], $job_item['template_id'], $job_item['notice_data'],
                    $job_item['openid'], '', $job_item['page']);
                $dispatch = dispatch($job);
                Log::info("模板消息内容:", $job_item);
                if ($job_item['type'] == 'wechat') {
                    Log::info("队列已添加:发送公众号模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                } elseif ($job_item['type'] == 'wxapp') {
                    Log::info("队列已添加:发送小程序订阅模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                }
            }
        }

    }
    /**
     * 组装Job任务需要的参数
     * @param $type
     * @param $room_name
     * @param $replay_info
     * @return array
     */
    private function makeJobParam($type, $users)
    {
        define('SIGN_PATH', '/pages/signin/index');//签到小程序地址

        $param = [];
        $jump_page = SIGN_PATH ;

        if ($type == 'wechat') {

            $first_value = '尊敬的用户:'.$users['wechat_nickname'].',每天签到领取健康金啦~';
            $remark_value = '尊敬的用户,坚持签到可领取健康金，点击领取共同守护家人健康~';

            $param['options'] = $this->options['wechat'];
            $param['page'] = $jump_page;
            $param['template_id'] = 'dxY9Gtbwb1A6uD56Ow6D7DvE7DIQESTr0jm7lv3BJQo';
            $param['notice_data'] = [
                'first' => ['value' => $first_value, 'color' => '#173177'],
                'keyword1' => ['value' => $users['wechat_nickname'], 'color' => '#173177'],
                'keyword2' => ['value' => $users['cumulative_number'], 'color' => '#173177'],
                'remark' => ['value' => $remark_value, 'color' => '#173177'],
            ];

        } elseif ($type == 'wxapp') {

            $thing1_value = '每日签到';
            $thing2_value = '尊敬的用户:'.$users['wxapp_nickname'].',每天签到领取健康金啦~';

            $param['options'] = $this->options['wxapp'];
            $param['page'] = $jump_page;
            $param['template_id'] = 'ZQzayZvME4-DaYnkHIBDzPNyttv738hpYkKA4iBbY5Y';
            $param['notice_data'] = [
                'thing1' => ['value' => $thing1_value, 'color' => '#173177'],
                'thing2' => ['value' => $thing2_value, 'color' => '#173177'],
                'name3' => ['value' => '坚持签到可领取健康金，点击领取共同守护家人健康~', 'color' => '#173177'],
            ];
        }

        return $param;
    }
}
