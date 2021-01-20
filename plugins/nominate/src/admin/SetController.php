<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/15
 * Time: 3:45 PM
 */

namespace Yunshop\Nominate\admin;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;

class SetController extends BaseController
{
    public function index()
    {
        $tempList = MessageTemp::getList();
        $set = \Setting::get('plugin.nominate');
        if (request()->isMethod('post')) {
            $setData = request()->setdata;
            $setData['plugin_name'] = $setData['plugin_name']?:'推荐奖励';
            $setData['nominate_prize_name'] = $setData['nominate_prize_name']?:'直推奖';
            $setData['nominate_poor_prize_name'] = $setData['nominate_poor_prize_name']?:'直推极差奖';
            $setData['team_prize_name'] = $setData['team_prize_name']?:'团队奖';
            $setData['team_manage_prize_name'] = $setData['team_manage_prize_name']?:'团队业绩奖';
            \Setting::set('plugin.nominate', $setData);
            return $this->message('保存成功', Url::absoluteWeb('plugin.nominate.admin.set.index'));
        }

        return view('Yunshop\Nominate::set.index', [
            'tempList'  => $tempList,
            'set'       => $set
        ])->render();
    }
}