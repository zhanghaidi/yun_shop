<?php

namespace Yunshop\Wechat\admin\fans\controller;

use app\common\components\BaseController;
use Yunshop\Wechat\admin\fans\model\Fans;
use Yunshop\Wechat\admin\fans\model\Member;
use Yunshop\Wechat\admin\fans\model\FansGroups;
use app\common\modules\wechat\WechatApplication;
use app\common\facades\Setting;
use Yunshop\Wechat\common\helper\Helper;
use Yunshop\Wechat\admin\fans\model\FansTagMapping;
use Yunshop\Wechat\jobs\SyncFansInfoJob;
use Yunshop\Wechat\service\FansService;

class FansController extends BaseController
{
    const PAGE_SIZE = 20;
    public function index()
    {
        return view('Yunshop\Wechat::admin.fans.index');
    }

    // 获取粉丝数据列表
    public function getFansList()
    {
        $groupId = request()->groupId;
        $follow = request()->follow;
        $keyword = request()->keyword;
        $page = (int)request()->page ? (int)request()->page : 1;
        // 获取粉丝列表，分页
        $search = Fans::uniacid()->with('hasOneMember');
        if (!empty($groupId)) {
            $search = $search->select('mc_mapping_fans.fanid','mc_mapping_fans.uid','mc_mapping_fans.openid','mc_mapping_fans.nickname'
                ,'mc_mapping_fans.groupid','mc_mapping_fans.follow','mc_mapping_fans.followtime','mc_mapping_fans.unfollowtime'
                ,'mc_mapping_fans.updatetime','mc_mapping_fans.unionid')
                ->join('mc_fans_tag_mapping','mc_mapping_fans.fanid','=','mc_fans_tag_mapping.fanid')
                ->where('mc_fans_tag_mapping.tagid','=',$groupId);
        }
        if ($follow === 0 || $follow === "0") {
            $search = $search->where('follow','=',0);
        } else {
            $search = $search->where('follow','=',1);
        }
        if ($keyword) {
            $search = $search->where('nickname','like',$keyword.'%');
        }
        $list = $search->orderBy('fanid','desc')->paginate(static::PAGE_SIZE,['*'],'page',$page);
        //->select('fanid','uid','openid','nickname','groupid','follow','followtime','updatetime','unionid')
        // 全部用户数
        $fansTotal = Fans::uniacid()->where('follow','=',1)->count();
        // 获取分组信息
        $groups = FansGroups::uniacid()->first();
        if (empty($groups)) {
            $groups = [];
        } else {
            $groups = $groups->toArray();
        }
        $list = $list->toArray();
        $list['groups'] = array_values($groups['groups']);
        $list['fansTotal'] = $fansTotal;
        $list['follow'] = $follow;
        $list['keyword'] = $keyword;
        $list['syncMember'] = Setting::get('wechat.fans.sync_member');
        $list['syncFans'] = Setting::get('wechat.fans.sync_fans');
        return json_encode($list);
    }

    // 同步选中粉丝信息，将选中的openid去微信获取用户详细数据，更新粉丝表，如果用户在同步设置中勾选了同步会员数据，那么会员表也要更新
    // 勾选的会员，最多一页数据，已设定一页20条数据
    public function syncBatch()
    {
        $fansIds = request()->fansIds;
        if (empty($fansIds)) {
            return $this->errorJson('请选择粉丝');
        }
        $openids = Fans::uniacid()->where('follow', '=', 1)->select('openid')->whereIn('fanid', $fansIds)->get();
        if (!empty($openids)) {
            $openids = $openids->toArray();
            $openids = array_column($openids, 'openid');
            \Log::info('------批量更新微信粉丝数据------',$openids);
            $fansService = new FansService();
            $fansService->updateFansInfo($openids);
            /*
            // 调用微信更新粉丝表和mc_members表
            $wechat = new WechatApplication();
            $userService = $wechat->user;
            try {
                $usersInfo = $userService->batchGet($openids);//获取用户信息
            } catch (\Exception $exception) {
                return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
            //更新数据库
            foreach ($usersInfo->user_info_list as $info) {
                // 更新mapping_fans表信息
                $fans = Fans::uniacid()->where('openid', '=',$info['openid'])->first();
                $fans->follow = $info['subscribe'];//0是未关注
                \Log::info('------批量更新微信粉丝数据------粉丝:openid'.$info['openid'].' subscribe:'.$info['subscribe']);
                if ($info['subscribe']) {
                    $fans->nickname = $info['nickname'];
                    $fans->followtime = $info['subscribe_time'];
                    $fans->unionid = $info['unionid'];
                    $fans->groupid = !empty($info['tagid_list']) ? $info['tagid_list'] : 0;
                    $fans->updatetime = time();
                }
                if(!$fans->save()) {
                    \Log::info('------粉丝信息保存失败------',$info);
                    \Log::error('------粉丝信息保存失败------',$info);
                } else {
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
                        \Log::info('------批量更新微信会员数据------会员ID:',$fans->uid);
                        $member = Member::uniacid()->where('uid','=',$fans->uid)->first();
                        if (!empty($member)) {
                            $member->nickname = $info['nickname'];
                            $member->avatar = $info['headimgurl'];
                            $member->nationality = $info['country'];
                            $member->resideprovince = $info['province'];
                            $member->residecity = $info['city'];
                            $member->gender = $info['sex'];
                            $member->save();
                        }
                    }
                }
            }
            */
        }
        return $this->successJson('更新成功');
    }
    // 更新所有粉丝信息，粉丝就是关注了公众号的人，
    // 需要从微信获取数据，然后插入或更新mapping_fans，插入或更新mc_members表，插入或更新fans_tag_mapping表
    public function syncAll()
    {
        \Log::info('------更新微信全部粉丝会员数据---start---');
        $fansService = new FansService();
        $result = $fansService->syncAll();
        if ($result['status'] == 1) {
            return $this->successJson($result['message']);
        } else {
            return $this->errorJson($result['message']);
        }
    }

    // 同步设置，更新粉丝记录时，是否更新会员数据
    public function syncSetting()
    {
        $flag = request()->syncMember;
        if ($flag) {
            Setting::set('wechat.fans.sync_member', 1);
        } else {
            Setting::set('wechat.fans.sync_member', 0);
        }
        return $this->successJson('保存成功');
    }

    // 粉丝同步设置，用于系统自动更新粉丝信息
    public function fansSyncSetting()
    {
        $flag = request()->syncFans;
        if ($flag) {
            Setting::set('wechat.fans.sync_fans', 1);
        } else {
            Setting::set('wechat.fans.sync_fans', 0);
        }
        return $this->successJson('保存成功');
    }

    // 添加分组，需要先请求微信创建分组，然后将微信分组存储本地
    public function addGroup()
    {
        $name = request()->name;
        if (empty($name)) {
            return $this->errorJson('组名不能为空');
        }
        // 调用微信创建分组
        $wechat = new WechatApplication();
        $tag = $wechat->user_tag;
        try {
            $tag->create($name);// 创建分组
            $list = $tag->lists();// 获取微信所有标签
            \Log::info('------创建粉丝分组------', $name);
        } catch (\Exception $exception) {
            return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        $groupInfo = FansGroups::uniacid()->first();
        if (empty($groupInfo)) {
            $groupInfo = new FansGroups();
            $groupInfo->uniacid = \Yunshop::app()->uniacid;
            $groupInfo->acid = \Yunshop::app()->uniacid;
        }
        $groupInfo->groups = !empty($list->tags) ? $list->tags : [];
        if($groupInfo->save()) {
            return $this->successJson('添加分组成功');
        } else {
            return $this->errorJson('添加分组失败');
        }
    }

    // 编辑分组
    public function editGroup()
    {
        $name = request()->name;
        $tagId = request()->id;
        if (empty($name) || empty($tagId)) {
            return $this->errorJson('组名不能为空');
        }
        // 调用微信创建分组
        $wechat = new WechatApplication();
        $tag = $wechat->user_tag;
        try {
            $tag->update($tagId, $name);
            $list = $tag->lists();// 获取微信所有标签
            \Log::info('------编辑粉丝分组------', $name);
        } catch (\Exception $exception) {
            return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        $groupInfo = FansGroups::uniacid()->first();
        if (empty($groupInfo)) {
            $groupInfo = new FansGroups();
            $groupInfo->uniacid = \Yunshop::app()->uniacid;
            $groupInfo->acid = \Yunshop::app()->uniacid;
        }
        $groupInfo->groups = !empty($list->tags) ? $list->tags : [];
        if($groupInfo->save()) {
            return $this->successJson('修改分组成功');
        } else {
            return $this->errorJson('修改分组失败');
        }
    }
    // 删除分组
    public function deleteGroup()
    {
        $tagId = request()->id;
        if (empty($tagId)) {
            return $this->errorJson('组名不能为空');
        }
        if ($tagId < 100) {
            return $this->errorJson('微信默认分组不能删除');
        }
        // 调用微信创建分组
        $wechat = new WechatApplication();
        $tag = $wechat->user_tag;
        try {
            $tag->delete($tagId);
            \Log::info('------删除粉丝分组------', $tagId);
        } catch (\Exception $exception) {
            return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        // 更新fans_groups分组表
        $this->syncGroup($wechat);

        $list = Fans::uniacid()->with(['hasOneFansTagMapping' => function ($query) use ($tagId) {
            return $query->where('tagid',$tagId);
        }])->join('mc_fans_tag_mapping','mc_mapping_fans.fanid','=','mc_fans_tag_mapping.fanid')
            ->where('mc_fans_tag_mapping.tagid','=',$tagId)->get();
        foreach ($list as $fans) {
            // 删除fans_tag_mapping
            if ($fans->hasOneFansTagMapping) {
                $fans->hasOneFansTagMapping->delete();
            }
            // 修改fans_mapping的groupid字段
            $fansGroupIds = explode(',',$fans->groupid);
            if(is_array($fansGroupIds)){
                foreach($fansGroupIds as $k=>$v){
                    if($v == $tagId){
                        unset($fansGroupIds[$k]);
                    }
                }
            }
            $fans->groupid = $fansGroupIds;
            $fans->save();
        }
        return $this->successJson('修改分组成功');
    }

    // 批量给会员打标签(包含对单个的操作)
    public function batchSetFansGroups()
    {
        $inputGroupIds = request()->groupIds;
        $inputFansIds= request()->fansIds;
        if (empty($inputFansIds)) {
            return $this->errorJson("粉丝不能为空");
        }
        if (empty($inputGroupIds)) {
            $inputGroupIds = [];
        }
        if (count($inputGroupIds) > 20) {
            return $this->errorJson("粉丝标签数不能大于20个");
        }
        // 获取openid,groupid
        $fansList = Fans::uniacid()->where('follow', '=', 1)->select('openid','groupid')->whereIn('fanid', $inputFansIds)->get();
        if (!empty($fansList)) {
            $fansList = $fansList->toArray();
            $openIds = array_column($fansList, 'openid');

            // 循环对每个粉丝做新增和删除操作
            foreach ($fansList as $fans) {
                // 求出新增的组
                $addGroups = array_filter(array_unique(array_diff($inputGroupIds,$fans['groupid'])));
                // 求出删除的组
                $deleteGroups = array_filter(array_unique(array_diff($fans['groupid'],$inputGroupIds)));

                \Log::info('------批量取消微信粉丝标签数据------修改前粉丝信息:', $fans);
                \Log::info('------批量取消微信粉丝标签数据------预期粉丝组:', $inputGroupIds);
                \Log::info('------批量取消微信粉丝标签数据------新增的组:', $addGroups);
                \Log::info('------批量取消微信粉丝标签数据------删除的组:', $deleteGroups);
                \Log::info('------批量更新微信粉丝标签数据------OPENID:', $fans['openid']);

                // 调用微信，将用户分组
                $wechat = new WechatApplication();
                $tag = $wechat->user_tag;
                // 对粉丝标签进行删除
                foreach ($deleteGroups as $deletaGroupId) {
                    \Log::info('------批量取消微信粉丝标签数据------标签ID:'.$deletaGroupId.' openid:', $fans['openid']);
                    try {
                        $tag->batchUntagUsers([$fans['openid']], $deletaGroupId);
                    } catch (\Exception $exception) {
                        return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
                    }
                }
                // 对粉丝标签进行新增
                foreach ($addGroups as $addGroupId) {
                    \Log::info('------批量新增微信粉丝标签数据------标签ID:'.$deletaGroupId.' openid:', $fans['openid']);
                    try {
                        $tag->batchTagUsers([$fans['openid']], $addGroupId);
                    } catch (\Exception $exception) {
                        return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
                    }
                }
            }
            // 打完标签后，要重新获取用户数据,更新用户的标签信息
            $this->syncUserInfo($wechat,$openIds);
            // 更新分组信息
            $this->syncGroup($wechat);

        }
        return $this->successJson("成功");
    }

    // 更新分组信息
    public function syncGroup($wechatApp)
    {
        $tag = $wechatApp->user_tag;
        try {
            // 解除用户和标签之间的关联
            $list = $tag->lists();// 获取微信所有标签
            \Log::info('------更新粉丝分组------', $list);
        } catch (\Exception $exception) {
            return $this->errorJson('更新粉丝分组失败:'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        // 修改fans_groups
        $groupInfo = FansGroups::uniacid()->first();
        if (empty($groupInfo)) {
            $groupInfo = new FansGroups();
            $groupInfo->uniacid = \Yunshop::app()->uniacid;
            $groupInfo->acid = \Yunshop::app()->uniacid;
        }
        $groupInfo->groups = !empty($list->tags) ? $list->tags : [];
        if ($groupInfo->save()) {
            return $groupInfo;
        } else {
            return $this->errorJson("更新分组失败");
        }
    }

    // 更新用户的标签信息，包括粉丝表的groupid，粉丝标签关联表，
    public function syncUserInfo($wechatApp, $openIds = [])
    {
        if (empty($openIds)) {
            return false;
        }
        $userService = $wechatApp->user;
        try {
            $usersInfo = $userService->batchGet($openIds);//获取用户信息
        } catch (\Exception $exception) {
            return $this->errorJson('更新粉丝信息失败:'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        foreach ($usersInfo->user_info_list as $info) {
            // 更新mapping_fans表信息
            $fans = Fans::uniacid()->where('openid', '=',$info['openid'])->first();
            $fans->follow = $info['subscribe'];//0是未关注
            \Log::info('------更新粉丝标签------粉丝:openid'.$info['openid'].' subscribe:'.$info['subscribe'],$info['tagid_list']);
            if ($info['subscribe']) {
                $fans->groupid = !empty($info['tagid_list']) ? $info['tagid_list'] : 0;
            }
            // 保存后要删除fans_tag_mapping表该粉丝数据并重新加上
            if ($fans->save()) {
                FansTagMapping::where('fanid','=',$fans->fanid)->delete();
                if (is_array($info['tagid_list'])) {
                    foreach ($info['tagid_list'] as $tag) {
                        $fansTagMapping = new FansTagMapping();
                        $fansTagMapping->fanid = $fans->fanid;
                        $fansTagMapping->tagid = $tag;
                        $fansTagMapping->save();
                    }
                }
            }
        }
        return true;
    }

}
