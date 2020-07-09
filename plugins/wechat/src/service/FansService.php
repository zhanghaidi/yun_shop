<?php
/**
 * Created by PhpStorm.
 * User: CHUWU
 * Date: 2019/5/20
 * Time: 11:09
 */

namespace Yunshop\Wechat\service;

use app\common\models\UniAccount;
use app\common\modules\wechat\WechatApplication;
use app\common\facades\Setting;
use Yunshop\Wechat\common\helper\Helper;
use Yunshop\Wechat\admin\fans\model\Fans;
use Yunshop\Wechat\admin\fans\model\FansTagMapping;
use Yunshop\Wechat\admin\fans\model\Member;
// 主要用于更新用户信息
class FansService
{
    public function handle()
    {
        // 获取所有公众号
        $uniAccount = UniAccount::get();
        // 循环每个公众号
        foreach ($uniAccount as $u) {
            // 设置公众号到系统
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $uniacid = \YunShop::app()->uniacid;
            // 获取该公众号的粉丝同步设置
            $flag = Setting::get('wechat.fans.sync_fans');
            \Log::info('------定时更新微信粉丝数据------公众号:'.$uniacid.' 同步设置:'.$flag);
            if ($flag) {//如果开启粉丝同步设置
                // 同步粉丝信息
                \Log::info('------定时更新微信粉丝数据开始------公众号:'.$uniacid);
                $result = $this->getOpenidList();
                \Log::info('------定时更新微信粉丝数据结束------公众号:'.$uniacid,$result);
            }
        }
    }

    public function syncAll()
    {
        //\Log::info('------更新微信全部粉丝数据开始------公众号:'.\YunShop::app()->uniacid);
        $result = $this->getOpenidList();
        //\Log::info('------更新微信全部粉丝数据结束------公众号:'.\YunShop::app()->uniacid,$result);
        return $result;
    }

    public function getOpenidList()
    {
        $wechat = new WechatApplication();
        $userService = $wechat->user;
        $flag = false;
        $next_openid = null;
        // 第一次请求，没有next_openid
        try {
            $firstList = $userService->lists();  // $nextOpenId 可选
        } catch (\Exception $exception) {
            \Log::info('------更新微信全部粉丝数据错误------'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            return ['status' => 0, 'message'=>Helper::getErrorMessage($exception->getCode(),$exception->getMessage())];
        }
        // ------------
        $openidList = $firstList->data['openid'];
        if (!empty($openidList)) {
            $result = $this->updateFansInfo($openidList);
            if ($result['status'] == 0) {
                return $result;
            }
        }
        // 后续请求要看看有没有next_openid
        if (!empty($firstList->next_openid)) {
            $next_openid = $firstList->next_openid;
            $flag = true;
        }
        while($flag && !empty($next_openid)) {
            try {
                $nextList = $userService->lists($next_openid);  // $nextOpenId 可选
            } catch (\Exception $exception) {
                \Log::info('------更新微信全部粉丝数据错误------next_openid:'.$next_openid.' '.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
                return ['status' => 0, 'message'=>Helper::getErrorMessage($exception->getCode(),$exception->getMessage())];
            }
            $openidList = $nextList->data['openid'];
            if (!empty($openidList)) {
                $result = $this->updateFansInfo($openidList);
                if ($result['status'] == 0) {
                    return $result;
                }
            }
            if (!empty($nextList['next_openid'])) {
                $next_openid = $nextList['next_openid'];
            } else {
                $flag = false;
                $next_openid = null;
            }
        }
        return ['status' => 1, 'message'=>'更新成功'];
    }

    // 批量更新粉丝和会员信息
    public function updateFansInfo($openidList = [])
    {
        $totalCount = count($openidList);
        $count = $totalCount < 100 ? $totalCount : 100;// 每次取最多一百个去请求微信
        $loopCount = ceil($totalCount/$count);
        for ($i = 0;$i < $loopCount; $i++) {
            $arr = array_slice($openidList,$i*$count, $count);
            $wechat = new WechatApplication();
            $userService = $wechat->user;
            try {
                $usersInfo = $userService->batchGet($arr);
            } catch (\Exception $exception) {
                \Log::info('------更新微信粉丝数据错误------'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
                return ['status' => 0, 'message'=>Helper::getErrorMessage($exception->getCode(),$exception->getMessage())];
            }
            foreach ($usersInfo->user_info_list as $info) {
                //\Log::info('------微信粉丝数据------粉丝信息:',$info);
                // 更新mapping_fans表信息
                $fans = Fans::uniacid()->where('openid', '=',$info['openid'])->first();
                if (empty($fans)) {
                    $newMember = new Member();// 创建一条基本的会员主表数据
                    $newMember->uniacid = \Yunshop::app()->uniacid;
                    $newMember->groupid = $info['groupid'];
                    $newMember->nickname = $info['nickname'];
                    $newMember->avatar = $info['headimgurl'];
                    $newMember->nationality = $info['country'];
                    $newMember->resideprovince = $info['province'];
                    $newMember->residecity = $info['city'];
                    $newMember->gender = $info['sex'];
                    $newMember->createtime = time();
                    if ($newMember->save()) {
                        \Log::info('------新增微信粉丝会员数据------粉丝:',$newMember);
                        $fans = new Fans();
                        $fans->uid = $newMember->uid;
                    } else {
                        return ['status' => 0, 'message' => '保存会员失败!'];
                    }
                }
                $fans->follow = $info['subscribe'];//0是未关注
                if ($info['subscribe']) {
                    $fans->uniacid = \Yunshop::app()->uniacid;
                    $fans->acid = \Yunshop::app()->uniacid;
                    $fans->openid = $info['openid'];
                    $fans->nickname = $info['nickname'];
                    $fans->followtime = $info['subscribe_time'];
                    $fans->unionid = !empty($info['unionid']) ? $info['unionid'] : '';
                    $fans->groupid = !empty($info['tagid_list']) ? $info['tagid_list'] : [0];
                    $fans->updatetime = time();
                }
                if($fans->save()) {
                    //\Log::info('------更新微信粉丝数据------粉丝:',$fans);
                    // 更新fans_tag_mapping表
                    FansTagMapping::where('fanid','=',$fans->fanid)->delete();
                    if (is_array($info['tagid_list'])) {
                        foreach ($info['tagid_list'] as $tag) {
                            $fansTagMapping = new FansTagMapping();
                            $fansTagMapping->fanid = $fans->fanid;
                            $fansTagMapping->tagid = $tag;
                            $fansTagMapping->save();
                        }
                    }
                    // 更新mc_members会员表信息
                    if (Setting::get('wechat.fans.sync_member') && $info['subscribe'] && !empty($fans->uid)) {
                        //\Log::info('------更新微信会员数据------会员ID:',$fans->uid);
                        $member = Member::uniacid()->where('uid','=',$fans->uid)->first();
                        if (!empty($member)) {
                            $member->groupid = $info['groupid'];
                            $member->nickname = $info['nickname'];
                            $member->avatar = $info['headimgurl'];
                            $member->nationality = $info['country'];
                            $member->resideprovince = $info['province'];
                            $member->residecity = $info['city'];
                            $member->gender = $info['sex'];
                            $member->save();
                        }
                    }
                } else {
                    \Log::info('------更新粉丝信息保存失败------', $info);
                    \Log::error('------更新粉丝信息保存失败------', $info);
                    return ['status' => 0, 'message'=>'更新粉丝信息保存失败'];
                }
            }
        }
        return ['status' => 1, 'message'=>'更新成功'];
    }
}