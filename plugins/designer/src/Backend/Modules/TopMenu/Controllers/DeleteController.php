<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/31
 * Time: 5:12 PM
 */

namespace Yunshop\Designer\Backend\Modules\TopMenu\Controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use Yunshop\Designer\Backend\Models\TopMenuModel;

class DeleteController extends BaseController
{
    /**
     * @var TopMenuModel
     */
    private $topMenuModel;


    public function index()
    {
        $this->topMenuModel = $this->topMenuModel();

        $result = $this->topMenuModel->delete();
        if ($result) {
            return $this->message("删除成功。", Url::absoluteWeb('plugin.designer.Backend.Modules.TopMenu.Controllers.records.index'));
        }
        return $this->message('数据失败，请重试1', '', 'error');
    }

    /**
     * @return TopMenuModel
     * @throws ShopException
     */
    private function topMenuModel()
    {
        $menu_id = $this->menuId();

        $topMenuModel = TopMenuModel::find($menu_id);
        if (!$topMenuModel) {
            throw new ShopException('未找到数据或以删除!');
        }
        return $topMenuModel;
    }

    /**
     * @return int
     * @throws ShopException
     */
    private function menuId()
    {
        $menu_id = (int)\YunShop::request()->menu_id;
        if (!$menu_id) {
            throw new ShopException('参数错误');
        }
        return (int)$menu_id;
    }
}
