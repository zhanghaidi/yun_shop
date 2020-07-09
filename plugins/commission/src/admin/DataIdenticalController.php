<?php
/**
 * Created by PhpStorm.
 * Date: 2017/3/16
 * Time: 下午5:38
 */

namespace Yunshop\Commission\admin;

use app\common\components\BaseController;
use Yunshop\Commission\admin\model\MemberShopInfo;
use Yunshop\Commission\admin\model\Agents;
use Illuminate\Support\Facades\DB;

class DataIdenticalController extends BaseController
{
    public function index()
    {
        // 获取需要操作的会员
        $members = MemberShopInfo::getAgentMembers();
        // 将会员数据增加到代理表
        Agents::dataIdentical($members);
        return "数据已同步!";
    }
}