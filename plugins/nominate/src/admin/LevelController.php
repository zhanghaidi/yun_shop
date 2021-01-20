<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/15
 * Time: 4:13 PM
 */

namespace Yunshop\Nominate\admin;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use Yunshop\Nominate\models\MemberChild;
use Yunshop\Nominate\models\NominateLevel;
use Yunshop\Nominate\models\ShopMemberLevel;

class LevelController extends BaseController
{
    public function index()
    {
        $list = ShopMemberLevel::select(['id', 'level', 'level_name'])
            ->orderBy('level', 'desc')
            ->get();

        return view('Yunshop\Nominate::level.index', [
            'list'  => $list
        ])->render();
    }

    public function detail()
    {
        $set = \Setting::get('plugin.mryt_set');
        $levelId = intval(request()->id);
        if (!$levelId) {
            throw new ShopException('参数错误');
        }
        $levelModel = ShopMemberLevel::select(['id', 'level', 'level_name'])
            ->with(['nominateLevel'])
            ->where('id', $levelId)
            ->first();
        $nominateLevel = $levelModel->nominateLevel;

        if (!$levelModel) {
            throw new ShopException('未找到该等级');
        }

        $levelList = ShopMemberLevel::select(['id', 'level', 'level_name'])
            ->orderBy('level', 'desc')
            ->get();

        return view('Yunshop\Nominate::level.detail', [
            'level'         => $levelModel,
            'nominateLevel' => $nominateLevel,
            'levelList'     => $levelList,
            'set'           => $set,
            'task'          => $levelModel->nominateLevel->task
        ])->render();
    }

    public function sub()
    {
        if (request()->isMethod('post')) {
            $levelData = request()->levelData;
            NominateLevel::store($levelData);

            return $this->message('保存成功', Url::absoluteWeb('plugin.nominate.admin.level.index'));
        }
    }
}