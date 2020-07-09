<?php

namespace Yunshop\Poster\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\services\Utils;
use Illuminate\Support\Facades\DB;
use Yunshop\Poster\models\Poster;
use app\common\models\frame\RuleKeyword;
use Yunshop\Poster\models\PosterSupplement;


class PosterController extends  BaseController
{
    /*
     * 查询海报
     */
    public function index()
    {
        // 判断是否新框架，否则走微擎
        if (config('APP_Framework') == 'platform') {
            if (!Utils::fieldexists('yz_wechat_rule', 'reply_type')) {
                DB::select("ALTER TABLE ". DB::getTablePrefix() . "yz_wechat_rule ADD `reply_type` TINYINT(1) Null DEFAULT '0';");
            }
        } else {
            //检查ims_rule表是否存在reply_type字段
            if (!Utils::fieldexists('rule', 'reply_type')) {
                DB::select("ALTER TABLE ". DB::getTablePrefix() . "rule ADD `reply_type` TINYINT(1) Null DEFAULT '0';");
            }
        }


        $title = \YunShop::request()->title;
        $type = \YunShop::request()->type;
        $pageSize = 10;

        if (empty($title) && empty($type)){
            $posters = Poster::getPosters()->orderBy('updated_at', 'desc')->paginate($pageSize);
        } else {
            $posters = Poster::getPostersBySearch($title, $type)->orderBy('updated_at', 'desc')->paginate($pageSize);
        }

        //海报总数
        $postersSum = $posters->total();

        $pager = PaginationHelper::show($postersSum, $posters->currentPage(), $posters->perPage());
        return view('Yunshop\Poster::admin.index',
                        [
                            'posters'=>$posters,
                            'posters_num'=>$postersSum,
                            'pager' => $pager,
                        ]
                    )->render();
    }

    /*
     * 创建海报
     */
    public function add()
    {
        if(!empty($_POST)){

            //确保记录在微擎框架中的关键词的唯一性
            $keyword = trim(\YunShop::request()->poster['keyword']);
            if (!empty($keyword)){
                $res = RuleKeyword::hasKeyword($keyword);
                if ($res){
                    return $this->message('关键词已经存在, 请重新设置', '', 'error');
                }
            }

/*预留给"活动海报"
            $posterRequest['type'] = \YunShop::request()->type;
            if ($posterRequest['type'] === Poster::TEMPORARY_POSTER){
                $posterRequest['time_start'] = strtotime(\YunShop::request()->time['start']);
                $posterRequest['time_end'] = strtotime(\YunShop::request()->time['end']);

                if ($posterRequest['time_end'] - $posterRequest['time_start'] <= 15
                    || ($posterRequest['time_end'] - $posterRequest['time_start']) / 86400 > 7) {
                    return $this->message('"活动海报"的有效期最短15秒最长7天', '', 'error');
                }
            }
*/
            //取值, 用于主表
            $posterRequest = \YunShop::request()->poster;
            $posterRequest['type'] = Poster::FOREVER_POSTER; //现阶段只有长期海报, 后期增加活动海报
            $posterRequest['uniacid'] = \YunShop::app()->uniacid;
            $posterRequest['background'] = tomedia(\YunShop::request()->poster['background']);
            $posterRequest['style_data'] = htmlspecialchars_decode(\YunShop::request()->data);
            $posterRequest['short_background'] = \YunShop::request()->poster['background'];
            $posterRequest['center_show'] = \YunShop::request()->poster['center_show'];
            $posterRequest['app_share_show'] = \YunShop::request()->poster['app_share_show'];
            if ($posterRequest['center_show'] == 1) {
                Poster::uniacid()->where('center_show',1)->update(['center_show'=> 0]);
            }

            if ($posterRequest['app_share_show'] == 1)
            {
                Poster::uniacid()->where("app_share_show",1)->update(['app_share_show'=>0]);
            }

            $posterModel = new Poster();
            $posterModel->fill($posterRequest);

            //取值, 用于辅表
            $posterRequest['supplement'] = \YunShop::request()->poster_supplement;
            $posterRequest['supplement']['recommender_credit'] = intval($posterRequest['supplement']['recommender_credit']) ?:0; //表单验证integer无效,暂时用intval转化
            $posterRequest['supplement']['recommender_bonus'] = $posterRequest['supplement']['recommender_bonus'] ?:0.00;
            $posterRequest['supplement']['recommender_coupon_num'] = intval($posterRequest['supplement']['recommender_coupon_num']) ?:0;
            $posterRequest['supplement']['subscriber_credit'] = intval($posterRequest['supplement']['subscriber_credit']) ?:0;
            $posterRequest['supplement']['subscriber_bonus'] = $posterRequest['supplement']['subscriber_bonus'] ?:0.00;
            $posterRequest['supplement']['subscriber_coupon_num'] = intval($posterRequest['supplement']['subscriber_coupon_num']) ?:0;

            $posterSupplementModel = new PosterSupplement();
            $posterSupplementModel->fill($posterRequest['supplement']);

            //表单验证
            $validator_01 = $posterModel->validator();
            $validator_02 = $posterSupplementModel->validator();
            //验证奖励金额
            $result = $this->validateBonus($posterRequest);
            if (!$result['status']) { //验证奖励金额失败
                $this->error($result['message']);
            } else if ($validator_01->fails()){ //验证失败
                $this->error($validator_01->messages());
            } elseif ($validator_02->fails()){ //验证失败
                $this->error($validator_02->messages());
            } elseif ($posterModel->save()){
                $posterSupplementModel->poster_id = $posterModel->id;
                if($posterSupplementModel->save()){
                    if (!file_exists(image_put_path().$posterRequest['short_background'])) {
                        \Curl::to($posterRequest['background'])->download(image_put_path().$posterRequest['short_background']);
                    }

                    return $this->message('海报创建成功', Url::absoluteWeb('plugin.poster.admin.poster.index'));
                }
            } else {
                $this->error('海报创建失败');
            }
        }

        return view('Yunshop\Poster::admin.edit',[
                    'poster' => $posterRequest,
                    'data' => $posterRequest['style_data'],
        ])->render();
    }

    /*
     * 编辑海报
     */
    public function edit()
    {

        $id = \YunShop::request()->poster_id;
        $posterModel = Poster::getPosterById($id);
        if (!$posterModel) {
            return $this->message('无此记录或者已被删除','','error');
        }

        //取出海报的设计数据
        $posterStyle = json_decode(str_replace('&quot;', '\'', $posterModel->style_data), true);

        if(!empty($_POST)){
            //检查关键词是否重复
            $keyword = trim(\YunShop::request()->poster['keyword']);
            $ruleId = RuleKeyword::hasKeyword($keyword);
            $previousRuleId = RuleKeyword::hasKeyword(trim($posterModel->keyword));
            if ($ruleId && ($ruleId != $previousRuleId)){
                return $this->message('关键词已经存在, 请重新设置', '', 'error');
            }

            //将"关键字"和"status"赋值给挂件，服务观察者
            $posterModel->widgets = [
                'keyword' => $posterModel->keyword,
                'status' => $posterModel->status,
            ];

/* 预留给活动海报
            $posterRequest['type'] = \YunShop::request()->type;
            if ($posterRequest['type'] === Poster::TEMPORARY_POSTER){
                $posterRequest['time_start'] = strtotime(\YunShop::request()->time['start']);
                $posterRequest['time_end'] = strtotime(\YunShop::request()->time['end']);

                if ($posterRequest['time_end'] - $posterRequest['time_start'] <= 15
                    || ($posterRequest['time_end'] - $posterRequest['time_start']) / 86400 > 7) {
                    return $this->message('"活动海报"的有效期最短15秒最长7天', '', 'error');
                }
            }
*/
            //取值, 用于主表
            $posterRequest = \YunShop::request()->poster;
            $posterRequest['background'] = tomedia(\YunShop::request()->poster['background']);
            $posterRequest['style_data'] = htmlspecialchars_decode(\YunShop::request()->data);

            if (preg_match('/^image/',  \YunShop::request()->poster['background'])) {
                $posterRequest['short_background'] = \YunShop::request()->poster['background'];
            }

            $posterRequest['center_show'] = \YunShop::request()->poster['center_show'];
            if ($posterRequest['center_show'] == 1 && $posterModel->center_show != 1) {
                Poster::uniacid()->where('center_show',1)->update(['center_show'=> 0]);
            }

            $posterRequest['app_share_show'] = \YunShop::request()->poster['app_share_show'];

            if ($posterRequest['app_share_show'] == 1 && $posterModel->app_share_show != 1)
            {
                Poster::uniacid()->where("app_share_show",1)->update(['app_share_show'=>0]);
            }

            $posterModel->fill($posterRequest);

            //取值, 用于辅表
            $posterRequest['supplement'] = \YunShop::request()->poster_supplement;
            $posterRequest['supplement']['recommender_credit'] = intval($posterRequest['supplement']['recommender_credit']) ?:0; //表单验证integer无效,暂时用intval转化
            $posterRequest['supplement']['recommender_bonus'] = $posterRequest['supplement']['recommender_bonus'] ?:0.00;
            $posterRequest['supplement']['recommender_coupon_num'] = intval($posterRequest['supplement']['recommender_coupon_num']) ?:0;
            $posterRequest['supplement']['subscriber_credit'] = intval($posterRequest['supplement']['subscriber_credit']) ?:0;
            $posterRequest['supplement']['subscriber_bonus'] = $posterRequest['supplement']['subscriber_bonus'] ?:0.00;
            $posterRequest['supplement']['subscriber_coupon_num'] = intval($posterRequest['supplement']['subscriber_coupon_num']) ?:0;

            $posterSupplementModel = PosterSupplement::getPosterSupplementByPosterId($id);
            $posterSupplementModel->fill($posterRequest['supplement']);

            //表单验证
            $validator_01 = $posterModel->validator();
            $validator_02 = $posterSupplementModel->validator();
            //验证奖励金额
            $result = $this->validateBonus($posterRequest);
            if (!$result['status']) { //验证奖励金额失败
                $this->error($result['message']);
            } else if ($validator_01->fails()){ //表单验证失败
                $this->error($validator_01->messages());
            } elseif ($validator_02->fails()){ //表单验证失败
                $this->error($validator_02->messages());
            } elseif ($posterModel->save() && $posterSupplementModel->save()){
                if (preg_match('/^image/',  \YunShop::request()->poster['background'])) {
                    if (!file_exists(image_put_path().$posterRequest['short_background'])) {
                        \Curl::to($posterRequest['background'])->download(image_put_path().$posterRequest['short_background']);
                    }
                }

                return $this->message('海报修改成功', Url::absoluteWeb('plugin.poster.admin.poster.index'));
            } else {
                return $this->message('海报修改失败','','error');
            }
        }
        $shop_credit1 = \Setting::get('shop.shop.credit1') ?: '积分';
        return view('Yunshop\Poster::admin.edit',
                        [
                            'poster'=>$posterModel,
                            'data'=>$posterStyle,
                            'shop_credit1' => $shop_credit1
                        ]
                    )->render();

    }

    /*
     * 删除海报
     */
    public function delete()
    {
        $id = \YunShop::request()->poster_id;
        if (!$id) {
            return $this->message('缺少参数','','error');
        }

        $posterModel = Poster::getPosterById($id);
        if (!$posterModel) {
            return $this->message('无此记录或者已被删除', '', 'error');
        }

        $keyword = $posterModel->keyword;

        $result = $posterModel->delete();
        if ($result) {
            if (RuleKeyword::hasKeyword($keyword)) {
                RuleKeyword::delKeyword($keyword);
            }

            return $this->message('删除海报成功', Url::absoluteWeb('plugin.poster.admin.poster.index'));
        } else {
            return $this->message('删除海报失败', '', 'error');
        }
    }

    private function updateCenterShow()
    {
        return Poster::uniacid()->where('center_show',1)->update(['center_show'=> 0]);
    }

    //验证奖励金额，必须为整数,数字两位小数，并且当选择微信钱包时，金额必须大于1
    public function validateBonus($posterRequest)
    {
        //1.奖励的金额应该为两位小数的数字
        //2.当选择微信钱包时，奖励的金额最小为1，也是两位小数
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $posterRequest['supplement']['recommender_bonus'])) {
            return ['status'=>0,'message'=>'奖励设置中,推荐者获得现金必须为数字且最多两位小数'];
        }
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $posterRequest['supplement']['subscriber_bonus'])) {
            return ['status'=>0,'message'=>'奖励设置中,关注者获得现金必须为数字且最多两位小数'];
        }
        if ($posterRequest['supplement']['bonus_method'] == 2) {
            if ($posterRequest['supplement']['recommender_bonus'] < 1) {
                return ['status'=>0,'message'=>'奖励设置中,选择微信钱包时,推荐者获得现金不能小于1'];
            }
            if ($posterRequest['supplement']['subscriber_bonus'] < 1) {
                return ['status'=>0,'message'=>'奖励设置中,选择微信钱包时,关注者获得现金不能小于1'];
            }
        }
        return ['status'=>1,'message'=>''];
    }



}