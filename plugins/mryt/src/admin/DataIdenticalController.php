<?php
/**
 * Created by PhpStorm.
 * Date: 2017/3/16
 * Time: 下午5:38
 */

namespace Yunshop\Mryt\admin;

use app\common\components\BaseController;
use Yunshop\Mryt\admin\model\MemberShopInfo;
use Yunshop\Mryt\admin\model\MrytMemberModel;
use Illuminate\Support\Facades\DB;

class DataIdenticalController extends BaseController
{
    public function index()
    {
        // 获取需要操作的会员
        $members = MemberShopInfo::getAgentMembers();
        // 将会员数据增加到每日一淘会员表
        MrytMemberModel::dataIdentical($members);
        return "数据已同步!";
    }
}