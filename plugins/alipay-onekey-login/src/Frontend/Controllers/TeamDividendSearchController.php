<?php


namespace Yunshop\Love\Frontend\Controllers;

use app\common\components\ApiController;
use Yunshop\TeamDividend\models\TeamDividendAgencyModel;
use Yunshop\Love\Common\Services\SetService;
use app\backend\modules\member\models\Member;
use app\common\models\member\MemberChildren;

class TeamDividendSearchController extends ApiController
{
    /**
     * 下级经销商查询和普通会员的查询
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // 获取需要搜索的内容
        $requestSearch = \YunShop::request();
        // 获取当前登录用户 id
        $member_id = \YunShop::app()->getMemberId();
        // 获取当前公众号 id
        $uniacid = \YunShop::app()->uniacid;

        if (!empty($requestSearch['search'])) {
            $list = $this->teamDividendChildren($member_id, $uniacid, $requestSearch);
        } else {
            return $this->errorJson('请输入您想搜索的值', []);
        }

        return $this->successJson('成功', $list);
    }

    /**
     *  查询当前经销商下级经销商及所有会员
     *
     */
    private function teamDividendChildren($member_id, $uniacid, $requestSearch)
    {
        $list = [];
        if (SetService::getLoveSet('transfer') == 1 && SetService::getLoveSet('team_dividend_transfer') == 1 && app('plugins')->isEnabled('team-dividend')) {

            // 所有下级经销商 id
            $ch_id = $this->teamDividendChildrenId($member_id, $uniacid);
//            dd($ch_id['0']);
            // 通过 id 模糊查询
            $list = $this->searchList($ch_id, $uniacid, $requestSearch);

            if (empty($list)) {
                return $this->errorJson('您输入的值没有查询到经销商或您暂时没有下级经销商', []);
            }
        } else if (SetService::getLoveSet('transfer') == 1) {
            // 给定一个空的id
            $ch_id = [];
            // 查询所有满足条件的用户
            $list = $this ->searchMember($uniacid, $requestSearch, $ch_id);

            if (empty($list)) {
                return $this->errorJson('输入的值没有搜索到对应的用户', []);
            }
        }

        return $list;
    }

    /**
     *  查询当前经销商下级所有经销商 id
     *
     */
    private function teamDividendChildrenId($member_id, $uniacid)
    {
        $ch_id = [];
        $me_id = [];
        $children = MemberChildren::with('hasOneMember')->where('member_id', $member_id)->where('uniacid', $uniacid)->get();

        foreach ($children as $child_id) {
            // 判断该经销商的下级是否为经销商
            $is_team_dividend = TeamDividendAgencyModel::where('uid', $child_id['child_id'])->first();
            if (!empty($is_team_dividend)) {
                $ch_id[] = $child_id['child_id'];
            }else {
                $me_id[] = $child_id['child_id'];
            }
        }

        // 查询的经销商不能等级不能大于当前登录经销商等级
        $ch_id = $this->isTeamDividendLevel($member_id, $uniacid, $ch_id);
        // 合并得到的两种不同id
        $children_id[] = $ch_id;
        $children_id[] = $me_id;

        return $children_id;
    }

    /**
     * 判断登录用户的经销商等级是否等于查询经销商等级(相等则不查询)
     *
     */
    private function isTeamDividendLevel($member_id, $uniacid, $ch_id)
    {
        // 查询登录经销商权重
        $getMemberLevel = TeamDividendAgencyModel::with('hasOneLevel')->where('uid', $member_id)->where( 'uniacid', $uniacid)->first()->hasOneLevel['level_weight'];
        // 查询下级所有经销商权重
        $getMemberChildrenLevel = TeamDividendAgencyModel::with('hasOneLevel')->whereIn('uid', $ch_id)->where( 'uniacid', $uniacid)->get();

        $child_id = [];
        foreach ($getMemberChildrenLevel as $levelId) {
            if ($getMemberLevel != $levelId['hasOneLevel']['level_weight']) {
                $child_id[] = $levelId['uid'];
            }
        }

        return $child_id;
    }

    /**
     *  通过所有下级经销商 id 进行模糊查询
     *
     */
    private function searchList($ch_id, $uniacid, $requestSearch)
    {
        $list = TeamDividendAgencyModel::with('hasOneMember')->whereHas('hasOneMember', function ($query) use ($requestSearch) {
            $query->where('uid', 'like', "%{$requestSearch['search']}%")
                ->orWhere('nickname', 'like', "%{$requestSearch['search']}%")
                ->orWhere('mobile', 'like', "%{$requestSearch['search']}%");
        })->whereIn('uid', $ch_id['0'])
            ->where('uniacid', $uniacid)
            ->get();

        // 筛选前端需要的字段
        $data = [];
        $i = 0;
        foreach ($list as $item) {
            $data[$i]['uid'] = $item['hasOneMember']['uid'];
            $data[$i]['nickname'] = $item['hasOneMember']['nickname'];
            if($data[$i]['nickname'] ==  "" || empty($data[$i]['nickname']) ){
                $data[$i]['nickname']='未更新-'.$data[$i]['uid'];
            }
            $i++;
        }

        // 将两个不同的数据拿出来
        $member = $this->searchMember($uniacid, $requestSearch, $ch_id['1']);
        foreach ($member as $item) {
            $data[$i]['uid'] = $item['uid'];
            $data[$i]['nickname'] = $item['nickname'];
            if($data[$i]['nickname'] ==  "" || empty($data[$i]['nickname']) ){
                $data[$i]['nickname']='未更新-'.$data[$i]['uid'];
            }
            $i++;
        }

        return $data;
    }

    /**
     *  查询所有会员
     *
     */
    private function searchMember($uniacid, $requestSearch, $ch_id)
    {
        if (empty($ch_id)) {
            $list = Member::where(function ($query) use ($requestSearch) {
                $query->where('uid', 'like', "%{$requestSearch['search']}%")
                    ->orWhere('nickname', 'like', "%{$requestSearch['search']}%")
                    ->orWhere('mobile', 'like', "%{$requestSearch['search']}%");
            })->where('uniacid', $uniacid)->get();
        } else {
            $list = Member::where(function ($query) use ($requestSearch) {
                $query->where('uid', 'like', "%{$requestSearch['search']}%")
                    ->orWhere('nickname', 'like', "%{$requestSearch['search']}%")
                    ->orWhere('mobile', 'like', "%{$requestSearch['search']}%");
            })->whereIn('uid', $ch_id)->where('uniacid', $uniacid)->get();
        }

        // 筛选前端需要的字段
        $data = [];
        $i = 0;
        foreach ($list as $item) {
            $data[$i]['uid'] = $item['uid'];
            $data[$i]['nickname'] = $item['nickname'];
            if($data[$i]['nickname'] ==  "" || empty($data[$i]['nickname']) ){
                $data[$i]['nickname']='未更新-'.$data[$i]['uid'];
            }
            $i++;
        }

        return $data;
    }
}
