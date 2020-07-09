<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-10
 * Time: 10:44
 */

namespace Yunshop\Designer\Backend\Modules\BottomMenu\Controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use Illuminate\Support\Facades\DB;
use Yunshop\Designer\Backend\Models\MenuModel;

class DefaultController extends BaseController
{
    /**
     * @var MenuModel
     */
    private $menuModel;


    public function index()
    {
        $this->menuModel = $this->menuModel();

        try {
            DB::transaction(function () {
                $this->updateMenuDefault();
            });

            return $this->successJson();

        } catch (\Exception $exception) {

            return $this->errorJson('操作失败，请刷新重试');
        }
    }

    private function updateMenuDefault()
    {
        $this->menuModel->is_default = $this->defaultStatus();

        return $this->menuModel->save();
    }


    /**
     * @return MenuModel
     * @throws ShopException
     */
    private function menuModel()
    {
        $menuModel = MenuModel::find($this->menuId());

        if (!$menuModel) {
            throw new ShopException('记录错误，请刷新重试！');
        }
        return $menuModel;
    }

    /**
     * 操作：off|on
     *
     * @return int
     */
    private function defaultStatus()
    {
        $op = request()->op;

        return $op == 'on' ? 1 : 0;
    }

    /**
     * @return mixed
     * @throws ShopException
     */
    private function menuId()
    {
        $menuId = request()->menu_id;
        if (!$menuId) {
            throw new ShopException('Menu id param is error!');
        }
        return $menuId;
    }
}
