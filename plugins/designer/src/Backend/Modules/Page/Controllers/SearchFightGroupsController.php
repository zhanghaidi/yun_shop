<?php

namespace Yunshop\Designer\Backend\Modules\Page\Controllers;


use app\common\components\BaseController;
use Yunshop\FightGroups\services\GroupService;

class SearchFightGroupsController extends BaseController
{
    public function index()
    {
        if ($this->fightGroupsPluginStatus()) {
            return $this->successJson('ok', $this->resultData());
        }
        return $this->errorJson('请先开启拼团活动插件');
    }

    private function resultData()
    {
        return ['fightGroupsList' => $this->fightGroupsList()];
    }

    private function fightGroupsList()
    {
        $list = GroupService::getFightGroupsInfo($this->keyword());
        return $list;
    }
    private function fightGroupsPluginStatus()
    {
        return app('plugins')->isEnabled('fight-groups') ? true : false;
    }

    private function keyword()
    {
        return (string)request()->input('keyword');
    }
}
