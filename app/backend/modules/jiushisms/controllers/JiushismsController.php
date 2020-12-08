<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午2:26
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\jiushisms\controllers;


use app\backend\modules\member\models\Member;
use app\common\components\BaseController;
use app\common\services\txyunsms\SmsSingleSender;
use Illuminate\Support\Facades\DB;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class JiushismsController extends BaseController
{
    //灸师列表
    public function jiushilist()
    {

        $input = \YunShop::request();
        $limit = 20;
        $where = [];
        if (isset($input->search)) {
            $search = $input->search;

            if (intval($search['jiushi_id']) > 0) {
                $where[] = ['id', '=', intval($search['jiushi_id'])];
            }
            if (trim($search['jiushi_name']) !== '') {
                $where[] = ['jiushi_name', 'like', '%' . trim($search['jiushi_name']) . '%'];
            }
            if (trim($search['jiushi_wechat']) !== '') {
                $where[] = ['jiushi_wechat', 'like', '%' . trim($search['jiushi_wechat']) . '%'];
            }
            if (trim($search['jiushi_wechat']) !== '') {
                $where[] = ['jiushi_wechat', 'like', '%' . trim($search['jiushi_wechat']) . '%'];
            }

        }
        $list = DB::table('jiushi_chat_chatuser')->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('jiushisms.jiushilist', [
            'count' => $list->total(),
            'pageList' => $list,
            'pager' => $pager,
            'request' => $input,
        ])->render();

    }

    //灸师编辑
    public function jiushiedit()
    {

        if (request()->isMethod('post')) {

            $param = request()->all();
            $id = $param['id'] ? $param['id'] : 0;
            $info = DB::table('jiushi_chat_chatuser')->where('id', $id)->first();

            if (!$info) {
                return $this->message('灸师不存在', Url::absoluteWeb(''), 'danger');
            }
            if (empty(trim($param['jiushi_name']))) {
                return $this->message('灸师真实姓名不能为空', Url::absoluteWeb(''), 'danger');
            }
            if (empty(trim($param['jiushi_wechat']))) {
                return $this->message('灸师微信不能为空', Url::absoluteWeb(''), 'danger');
            }
            $upd_data = [];
            $upd_data['jiushi_name'] = $param['jiushi_name'];
            $upd_data['jiushi_wechat'] = $param['jiushi_wechat'];

            $res = DB::table('jiushi_chat_chatuser')->where('id', $id)->update($upd_data);
            if ($res) {
                return $this->message('编辑成功！', Url::absoluteWeb('jiushisms.jiushisms.jiushilist'), 'success');
            }
            return $this->message('编辑失败！', Url::absoluteWeb(''), 'danger');
        }
        //接收参数
        $id = request()->get('id', 0);

        $info = DB::table('jiushi_chat_chatuser')->where('id', $id)->first();
        if (!$info) {
            return $this->message('灸师不存在', Url::absoluteWeb(''), 'danger');
        }

        return view('jiushisms.jiushiedit', [
            'id' => $id,
            'info' => $info,
        ])->render();

    }

    //发送短信
    public function sendsms()
    {

        if (request()->isMethod('post')) {
            try {
                //sms_send 是否开启
                $smsSet = \Setting::get('shop.sms');
                //是否设置
                if ($smsSet['type'] != 5 || empty($smsSet['tx_templateJiushiSmsCode'])) {
                    return $this->message('请先配置短信模板id', Url::absoluteWeb(''), 'danger');
                }
                $post = request()->all();
                if (empty($post['jiushi_wechat'])) {
                    return $this->message('灸师企业微信号不能为空', Url::absoluteWeb(''), 'danger');
                }
                $info = DB::table('jiushi_chat_chatuser')->where('id', $post['jiushi_wechat'])->first();
                if (!$info) {
                    return $this->message('灸师不存在', Url::absoluteWeb(''), 'danger');
                }
                $mobile = trim($post['mobile']);
                if (empty($mobile)) {
                    return $this->message('接收手机号不能为空', Url::absoluteWeb(''), 'danger');
                }

                //组装变量
                $param = [$info['jiushi_wechat'], '100'];
                //初始化发短息类
                $ssender = new SmsSingleSender(trim($smsSet['tx_sdkappid']), trim($smsSet['tx_appkey']));
                $response = $ssender->sendWithParam('86', $mobile, $smsSet['tx_templateJiushiSmsCode'],
                    $param, $smsSet['tx_signname'], "", "");  // 签名参数不能为空串
                $response = json_decode($response);

                if ($response->result == 0 && $response->errmsg == 'OK') {
                    //插入短信记录表
                    $insert_data = [
                        'uniacid' => 39,
                        'mobile' => $mobile,
                        'jiushi_wechat' => $info['jiushi_wechat'],
                        'jiushi_id' => $post['jiushi_wechat'],
                        'result' => $response->result,
                        'result_error_msg' => $response->errmsg,
                        'createtime' => time()
                    ];
                    DB::table('yz_sendsms_log')->insert($insert_data);
                    return $this->message('发送成功！', Url::absoluteWeb('jiushisms.jiushisms.sendsms'), 'success');
                } else {
                    \Log::debug($response->errmsg);
                    $insert_data = [
                        'uniacid' => 39,
                        'mobile' => $mobile,
                        'jiushi_wechat' => $info['jiushi_wechat'],
                        'jiushi_id' => $post['jiushi_wechat'],
                        'result' => $response->result,
                        'result_error_msg' => $response->errmsg,
                        'createtime' => time()
                    ];
                    DB::table('yz_sendsms_log')->insert($insert_data);
                    return $this->message('发送失败！' . $response->errmsg, Url::absoluteWeb('jiushisms.jiushisms.sendsms'), 'danger');
                }
            } catch (\Exception $e) {
                return $this->message('发送失败！' . $response->errmsg, Url::absoluteWeb(''), 'danger');
            }
        }
        //查询灸师微信号
        $jiushi_wechat_list = DB::table('jiushi_chat_chatuser')->select('id', 'jiushi_wechat')->where('jiushi_wechat', '<>', '')->orderBy('id', 'desc')->get()->toArray();

        return view('jiushisms.sendsms', [
            'wechat_list' => $jiushi_wechat_list,
        ])->render();
    }

//短信记录
    public function smslist()
    {

        $input = \YunShop::request();
        $limit = 20;

        // 处理搜索条件
        $where = [];
        $where_between = ['createtime', [0, strtotime('20991231')]];
        if (isset($input->search)) {
            $search = $input->search;

            if (intval($search['jiushi_id']) > 0) {
                $where[] = ['jiushi_id', '=', intval($search['jiushi_id'])];
            }
            if ($search['result'] !== '') {
                if (intval($search['result']) == 0) {
                    $where[] = ['result', '=', intval($search['result'])];
                }
                if (intval($search['result']) > 0) {
                    $where[] = ['result', '>', 0];
                }
            }
            if ($search['friends_status'] !== '') {
                $where[] = ['friends_status', '=', intval($search['friends_status'])];
            }
            if (trim($search['jiushi_name']) !== '') {
                $where[] = ['jiushi_name', 'like', '%' . trim($search['jiushi_name']) . '%'];
            }
            if (trim($search['jiushi_wechat']) !== '') {
                $where[] = ['jiushi_wechat', 'like', '%' . trim($search['jiushi_wechat']) . '%'];
            }
            if (trim($search['jiushi_wechat']) !== '') {
                $where[] = ['jiushi_wechat', 'like', '%' . trim($search['jiushi_wechat']) . '%'];
            }
            if ($search['searchtime'] !== '') {
                $time_field = ($search['searchtime'] === '0') ? 'createtime' : 'updatetime';
                $where_between[0] = $time_field;
                $where_between[1] = [strtotime($search['date']['start']), strtotime($search['date']['end'] . ' 23:59:59')];
            }

        }

        $list = DB::table('yz_sendsms_log')->where($where)
            ->whereBetween($where_between[0], $where_between[1])
            ->orderBy('id', 'desc')
            ->paginate($limit);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        //发送短信的总条数
        $count_total = DB::table('yz_sendsms_log')->where('result', 0)->where($where)->whereBetween($where_between[0], $where_between[1])->count();
        $friends_total = DB::table('yz_sendsms_log')->where('result', 0)->where('friends_status', 1)->where($where)->whereBetween($where_between[0], $where_between[1])->count();//加友成功的条数
        $success_percentage = 0;
        if ($count_total > 0) {
            $success_percentage = floatval($count_total / $friends_total);
        }

        return view('jiushisms.smslist', [
            'count' => $list->total(),
            'pageList' => $list,
            'pager' => $pager,
            'request' => $input,
            'success_percentage' => $success_percentage * 100
        ])->render();

    }

//更新加友状态
    public function jiushifriendsstatus()
    {

        $param = request()->all();
        $id = $param['id'] ? $param['id'] : 0;
        $info = DB::table('yz_sendsms_log')->where('id', $id)->first();
        if (!$info) {
            return $this->message('记录不存在', Url::absoluteWeb(''), 'danger');
        }
        $upd_data = [];
        $upd_data['friends_status'] = $param['friends_status'];
        $upd_data['updatetime'] = time();
        $res = DB::table('yz_sendsms_log')->where('id', $id)->update($upd_data);
        if ($res) {
            return $this->message('更新成功！', Url::absoluteWeb('jiushisms.jiushisms.smslist'), 'success');
        }
        return $this->message('更新失败！', Url::absoluteWeb(''), 'danger');
    }
}
