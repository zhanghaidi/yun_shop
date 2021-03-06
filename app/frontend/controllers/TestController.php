<?php

namespace app\frontend\controllers;

use app\common\components\BaseController;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\Option;
use app\common\models\Order;
use app\common\modules\goods\GoodsRepository;
use app\common\modules\option\OptionRepository;
use app\frontend\models\Goods;
use app\Jobs\addGoodsCouponQueueJob;
use app\common\models\live\CloudLiveRoom;
use Yunshop\Love\Modules\Goods\GoodsLoveRepository;
use app\Jobs\SendTemplateMsgJob;
use Illuminate\Support\Facades\DB;
use app\framework\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use QrCode;

class TestController extends BaseController
{
    public function __construct()
    {
        // 公众号
        $wechat_account = DB::table('account_wechats')
            ->select('key', 'secret')
            ->where('uniacid', 39)
            ->first();
        $this->options['wechat'] = [
            'app_id' => $wechat_account['key'],
            'secret' => $wechat_account['secret'],
        ];

        // 商城小程序
        $wxapp_account = DB::table('account_wxapp')
            ->select('key', 'secret')
            ->where('uniacid', 47)
            ->first();
        $this->options['wxapp'] = [
            'app_id' => $wxapp_account['key'],
            'secret' => $wxapp_account['secret'],
        ];
    }
    public function test(){
//        return json([1,2,3]);
        $queueData = [
            'uniacid' => \YunShop::app()->uniacid,
            'goods_id' => 'laoge001',
            'uid' => 'uid-123321',
            'coupon_id' => 'coupon-456654',
            'send_num' => '123',
            'end_send_num' => 0,
            'status' => 0,
            'created_at' => time()
        ];
        $this->dispatch((new addGoodsCouponQueueJob($queueData)));
        return json([1,2,3]);
    }
    public function index()
    {
        $order = Order::find(45751);
        event(new AfterOrderPaidEvent($order));
//        $goods = Goods::find(1175);
//        $goods->reduceStock(1);
        return $this->successJson();
//        $goods->stock = max($goods->stock - 1, 0);
//        $goods->save();
//        $stock = file_get_contents(storage_path('app/test'));
//        $stock = max($stock - 1, 0);
//        file_put_contents(storage_path('app/test'), $stock . PHP_EOL);

    }

    public function testlive(){
        $time_now = time();
        $wait_seconds = 60 * 1;
        $check_time_range = [$time_now - $wait_seconds - 60,$time_now - $wait_seconds];

        // 1、查询开始时间距离当前时间2分钟之内开播的直播 where('live_status', 101)暂时不卡播放状态 where('start_time', $check_time_range)->
        $startLiveRoom = CloudLiveRoom::whereBetween('start_time', $check_time_range)->select('id','name','live_status','start_time','anchor_name')->with('hasManySubscription')->get()->toArray();


        //查询订阅开播直播间的用户
        foreach ($startLiveRoom as $room) {
            if(!empty($room['has_many_subscription'])) {
                foreach ($room['has_many_subscription'] as $value){
                    $user = DB::table('diagnostic_service_user')->select('ajy_uid','shop_openid','unionid')->where('ajy_uid', $value['user_id'])->first();
                    $fans = DB::table('mc_mapping_fans')->select('uid', 'openid')->where(['uid' => $value['user_id'], 'follow' => 1])->first();
                    $user['openid'] = '';
                    if($fans){
                        $user['openid'] = $fans['openid'];
                    }

                    $type = $user['openid'] ? 'wechat' : 'wxapp';

                    $openid = $user['openid'] ? $user['openid'] : $user['shop_openid'];

                    $job_param = $this->makeJobParam($type, $room);
                    Log::info("模板消息内容:".$openid.$type, $job_param);

                    $job = new SendTemplateMsgJob($type, $job_param['options'], $job_param['template_id'], $job_param['notice_data'], $openid, '', $job_param['page'], $job_param['miniprogram']);
                    $dispatch = dispatch($job);

                    Log::info("队列已添加:".$type, ['job' => $job, 'dispatch' => $dispatch]);

                }
            }
        }

        Log::info("------------------------ 小程序签到提醒定时任务 END -------------------------------\n");
    }

    /**
     * 组装Job任务需要的参数
     * @param $type
     * @param $room_name
     * @param $replay_info
     * @return array
     */
    private function makeJobParam($type, $room)
    {
        define('CLOUD_LIVE_PATH', '/pages/cloud-live/live-player/live-player?tid='); //云直播间

        $param = [];
        $jump_page = '/pages/template/shopping/index?share=1&shareUrl=';

        $jump_tail = CLOUD_LIVE_PATH . $room['id']; //直播间路径

        if ($type == 'wechat') {

            $first_value = '尊敬的用户,您订阅的直播间开始直播啦~';
            $remark_value = '【' . $room['name'] . '】正在进行中,观看直播互动享更多福利优惠~';

            $param['options'] = $this->options['wechat'];
            $param['page'] = $jump_tail;
            $param['template_id'] = 'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE'; //课程进度提醒模板
            $param['notice_data'] = [
                'first' =>  ['value' => $first_value, 'color' => '#173177'],
                'keyword1' => ['value' => '【' . $room['name'] . '】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '开播中', 'color' => '#173177'],
                'remark' => ['value' => $remark_value, 'color' => '#173177'],
            ];
            $param['miniprogram'] =[
                'miniprogram' => [
                    'appid' => $this->options['wxapp']['app_id'],
                    'pagepath' => $param['page']
                ]
            ];


        } elseif ($type == 'wxapp') {

            $thing1_value = '直播间开播提醒';

            $param['options'] = $this->options['wxapp'];
            $param['page'] = $jump_tail;
            $param['template_id'] = 'ABepy-L03XH_iU0tPd03VUV9KQ_Vjii5mClL7Qp8_jc';
            $param['notice_data'] = [
                'thing1' => ['value' => $thing1_value, 'color' => '#173177'],
                'thing2' => ['value' => '【' . $room['name'] . '】', 'color' => '#173177'],
                'name3' => ['value' => $room['anchor_name'], 'color' => '#173177'],
                'thing4' => ['value' => date('Y-m-d H:i', $room['start_time']), 'color' => '#173177'],
            ];
        }

        return $param;
    }
// 获取一周的时间
    public function testGetWeekTime()
    {

        $week = date('w', time());//周日是零

        $weekname = array('星期日','星期一', '星期二', '星期三', '星期四', '星期五', '星期六');
        //星期日排到末位
//        if (empty($week)) {
//            $week = 7;
//        }
        for ($i = 0; $i <= 6; $i++) {
            $data[$i]['date'] = date('Y-m-d', strtotime('+' . $i + 1 - $week . ' days', time()));
            $data[$i]['week'] = $weekname[$i];
        }
        return $data;
    }


    public function testImageMake(){

        $member_id = \YunShop::app()->getMemberId();
        $url = config('app.url').'/web/reg/index.html';
        $disk = 'image';
        $querys = '';
        $querys .= '?recommend=' . $member_id;

        $fileName = 'images/share/share.jpg';

        $qrcode = env('IMG_URL').'/' .$fileName;

        try{
            if(file_get_contents($qrcode)){
                return __return($this->successStatus, '获取成功',$qrcode);
            }
        } catch(\Exception $e){

        }

        if(!Storage::exists('share_poster')){
            Storage::disk()->makeDirectory('share_poster');
        }

        $png = QrCode::format('png')->size(120)->margin(0)
            ->generate($url . $querys);

        Storage::disk($disk)->put($fileName,$png);

        // 修改指定图片的大小share_en.jpg
        $img = Image::make(env('IMG_URL').'/share/share_zh.jpg')->resize(750,1334);

        $amount_profit = '测试谁呀';

        $img->text($amount_profit, 320, 580, function($font) {
            $font->file(base_path().'/public/fonts/msyhl.ttc');
            $font->size(34);
            $font->color('#747474');
            $font->align('center');
            // $font->valign('top');
            // $font->angle(45);
        });

        // 插入水印, 水印位置在原图片的左下角, 距离下边距 10 像素, 距离右边距 15 像素
        $img->insert($qrcode, 'bottom-right', 5, 5);

        // 将处理后的图片重新保存到其他路径
        $img->save($fileName);

        Storage::disk($disk)->put($fileName,file_get_contents($fileName));

        if(file_exists($fileName)){
            unlink ($fileName);
        }

        return __return($this->successStatus, '获取成功',$qrcode);
    }


}