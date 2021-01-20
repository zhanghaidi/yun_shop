<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/23
 * Time: 下午5:57
 */

namespace Yunshop\Community\admin;

use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use Illuminate\Support\Facades\Schema;
use Yunshop\Community\models\AgrGroup;

class HandleController extends BaseController
{
    private $member_levels;

    public function index()
    {
        if (request()->isMethod('post')) {
            // 判断 圈子用户权限组 数据表是否存在
            $this->verifyCommunityTables();
            // 获取商城会员等级
            $this->getMemberLevels();
            // 同步
            $this->handle();
            return $this->message('同步成功', Url::absoluteWeb('plugin.community.admin.handle.index'));
        }

        return view('Yunshop\Community::admin.handle', [

        ])->render();
    }

    /**
     * @name 同步
     * @author
     */
    private function handle()
    {
        foreach ($this->member_levels as $level) {
            $this->handleRow($level);
        }
    }

    /**
     * @name 执行每行
     * @author
     * @param $level
     */
    private function handleRow($level)
    {
        $agr_group = AgrGroup::getGroupByShopLevelId($level->id, $level->uniacid)->first();
        if (!$agr_group) {
            AgrGroup::create([
                'title' => $level->level_name,
                'create_time' => time(),
                'acid' => $level->uniacid,
                'shop_level_id' => $level->id,
            ]);
        } else {
            if ($level->level_name != $agr_group->title) {
                $agr_group->title = $level->level_name;
                $agr_group->save();
            }
        }
    }

    /**
     * @name 判断表是否存在
     * @author
     * @throws ShopException
     */
    private function verifyCommunityTables()
    {
        $existence = Schema::hasTable('agr_group');
        if (!$existence) {
            throw new ShopException('未安装圈子社区模块');
        }
    }

    /**
     * @name 获取商城会员等级
     * @author
     * @throws ShopException
     */
    private function getMemberLevels()
    {
        $this->member_levels = MemberLevel::select('id', 'level_name', 'uniacid')->get();
        if ($this->member_levels->isEmpty()) {
            throw new ShopException('商城等级为空');
        }
    }
}