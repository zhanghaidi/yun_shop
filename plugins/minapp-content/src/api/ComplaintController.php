<?php

namespace Yunshop\MinappContent\api;

use app\backend\modules\tracking\models\DiagnosticServiceUser;
use app\common\components\ApiController;
use Illuminate\Support\Facades\Cache;
use Yunshop\MinappContent\models\ComplainModel;
use Yunshop\MinappContent\models\ComplainTypeModel as ComplainType;
use Illuminate\Support\Facades\DB;

//用户投诉|反馈控制器
class ComplaintController extends ApiController
{
    protected $publicAction = ['getComplainType'];
    protected $ignoreAction = ['getComplainType'];

    protected $user_id = 0;
    protected $uniacid = 0;

    /**
     *  constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->uniacid = \YunShop::app()->uniacid;
        $this->user_id = \YunShop::app()->getMemberId();
    }

    /**
     * 获取投诉类型
     */
    public function getComplainType()
    {
        $cache_key = 'ajy_service_complainType' . $this->uniacid;
        $complainType = Cache::get($cache_key);
        if (!$complainType) {
            $complainType = ComplainType::where(['uniacid' => $this->uniacid, 'status' => 1])->select('id', 'name')->orderBy('list_order', 'DESC')->get();
            Cache::forget($cache_key);
            Cache::add($cache_key, $complainType, 10);
        }

        return $this->successJson('投诉类型获取成功', $complainType);
    }

    /**
     * 用户投诉
     */
    public function userComplain()
    {
        $complain_type = intval(request()->get('complain_type', 0));
        $content = trim(request()->get('content', ''));
        $info_id = intval(request()->get('info_id', 0));
        $to_type_id = intval(request()->get('to_type_id', 0));
        $images = html_entity_decode(request()->get('pictures', ''));
        if (!$info_id) {
            return $this->errorJson('投诉对象不能为空');
        }
        if (!$complain_type) {
            return $this->errorJson('请选择投诉类型');
        }
        if (empty($content)) {
            return $this->errorJson('投诉内容不能为空');
        }
        if (!$to_type_id) {
            return $this->errorJson('投诉对象类型不能为空');
        }
        $data = array(
            'uniacid' => $this->uniacid,
            'user_id' => $this->user_id,
            'images' => $images,
            'type' => $complain_type,
            'info_id' => $info_id,
            'to_type_id' => $to_type_id,
            'content' => $content,
            'create_time' => TIMESTAMP
        );
        $res = ComplainModel::insert($data);
        if ($res) {
            return $this->successJson('投诉成功,请耐心等待客服解决', array('status' => 1));
        } else {
            return $this->errorJson('投诉失败', array('status' => 0));
        }
    }

    /**
     * 用户反馈
     */
    public function userFeedback()
    {
        $content = trim(request()->get('content', ''));
        if (empty($content)) {
            return $this->errorJson('请输入反馈内容');
        }
        $telephone = trim(request()->get('telephone', ''));
        if (!$telephone) {
            return $this->errorJson('请输入手机号');
        }
        if (!preg_match('/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/', $telephone)) {
            return $this->errorJson('请填写正确手机号');
        }
        $images = html_entity_decode(request()->get('pictures', ''));

        $cache_key = $this->uniacid . $this->user_id;
        $cache = Cache::get($cache_key);
        if ($cache) {
            return $this->successJson('您已经反馈，请在1分钟后再反馈', array('status' => 2));
        } else {
            //获取用户信息
            $user = DiagnosticServiceUser::where('uniacid', $this->uniacid)->find($this->user_id);
            if (empty($user)) {
                return $this->errorJson('用户信息获取失败');
            }
            $data = array(
                'uniacid' => $this->uniacid,
                'user_id' => $this->user_id,
                'content' => $content,
                'telephone' => $telephone,
                'images' => $images,
                'nickname' => $user['nickname'],
                'avatarurl' => $user['avatarurl'],
                'gender' => $user['gender'],
                'country' => $user['country'],
                'province' => $user['province'],
                'city' => $user['city'],
                'account' => $user['account'],
                'add_time' => TIMESTAMP
            );
            $res = DB::table('diagnostic_service_feedback')->insert($data);
            if ($res) {
                Cache::add($cache_key, $data['content'], 1);
                return $this->successJson('反馈留言成功', array('status' => 1));
            } else {
                return $this->errorJson('反馈留言失败', array('status' => 0));
            }
        }
    }

    /**
     * 违规图片上报
     * @return mixed
     */
    public function postImageCheck()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $image = request()->get('image', '');
        if(!$image){
            return $this->errorJson('上报图片不能为空');
        }
        $data = [
            'uniacid' => $uniacid,
            'user_id' => $user_id,
            'image' => $image,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s')
        ];
        $res = DB::table('diagnostic_service_sns_upload_filter')->insertGetId($data);
        if (!$res) {
            return $this->errorJson('上报失败');
        }
        return $this->successJson('上报成功');
    }
}
