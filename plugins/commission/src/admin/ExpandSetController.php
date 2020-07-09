<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/9
 * Time: 17:02
 */

namespace Yunshop\Commission\admin;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Commission\models\AgentLevel;

class ExpandSetController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $set = \Setting::get('plugin.commission_expand');
        $request = \YunShop::request()->set;
        $levels = AgentLevel::uniacid()->get();

        if ($request) {
            $request['is_expand'] = $set['is_expand'];
            if (\Setting::set('plugin.commission_expand', $request)) {
                return $this->message('设置成功');
            } else {
                $this->error('设置失败');
            }
        }

        return view('Yunshop\Commission::admin.expand_set', [
            'set' => $set,
            'levels' => $levels,
            'defaultlevelname' => AgentLevel::getDefaultLevelName()
        ])->render();
    }

}