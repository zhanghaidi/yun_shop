<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/22
 * Time: 上午11:07
 */

namespace Yunshop\Designer\Backend\Modules\TopMenu\Controllers;


use app\common\components\BaseController;
use Yunshop\Designer\Backend\Models\TopMenuModel;

class StoreController extends BaseController
{
    /**
     * @var TopMenuModel
     */
    private $topMenuModel;


    /**
     *
     */
    public function preAction()
    {
        parent::preAction();
        $this->topMenuModel = $this->getTopMenuModel();
    }


    public function index()
    {
        $menus = $this->menus();
        $params = $this->params();
        $menu_name = $this->menuName();
        if ($menus && $params && $menu_name) {
            return $this->store();
        }
        return view('Yunshop\Designer::topMenu.store', $this->getResultData());
    }

    private function getResultData()
    {
        return [
            'menuModel' => $this->topMenuModel,
            'params'    => !empty($this->topMenuModel->params) ? $this->topMenuModel->params : $this->defaultParams(),
            'menus'     => !empty($this->topMenuModel->menus) ? $this->topMenuModel->menus : $this->defaultMenus()
        ];
    }


    private function store()
    {
        $result = $this->_store();

        if ($result === true) {
            die(json_encode(array('result' => 1,)));
        }
        die(json_encode(array('result' => 0, 'message' => $result)));
    }

    /**
     * @return bool|string
     */
    private function _store()
    {
        $this->topMenuModel->fill($this->getTopMenuData());
        $validator = $this->topMenuModel->validator();
        if ($validator->fails()) {
            return $validator->messages()->first();
        }
        return $this->topMenuModel->save();
    }

    /**
     * @return array
     */
    private function getTopMenuData()
    {
        $menus = $this->menus();
        $params = $this->params();
        $menu_name = $this->menuName();

        return [
            'uniacid'       => \YunShop::app()->uniacid,
            'menu_name'     => $menu_name,
            'menus'         => htmlspecialchars_decode($menus),
            'params'        => htmlspecialchars_decode($params)
        ];
    }

    /**
     * @return TopMenuModel
     */
    private function getTopMenuModel()
    {
        $topMenuModel = null;

        $menu_id = $this->menuId();
        if ($menu_id) {
            $topMenuModel = TopMenuModel::find($menu_id);
        }
        return $topMenuModel ?: new TopMenuModel();
    }

    /**
     * @return string
     */
    private function menus()
    {
        return \YunShop::request()->menus;
    }

    /**
     * @return string
     */
    private function params()
    {
        return \YunShop::request()->params;
    }

    /**
     * @return string
     */
    private function menuName()
    {
        return \YunShop::request()->menu_name;
    }

    /**
     * @return int
     */
    private function menuId()
    {
        return (int)\YunShop::request()->menu_id;
    }

    /**
     * @return false|mixed|string
     */
    private function defaultParams()
    {
        $data = array(
            "bgcolor"           => '#fafafa',
            "bgcolorhigh"       => '#fafafa',
            "bordercolor"       => '#bfbfbf',
            "previewbg"         => '#999999',
            "showborder"        => 1,
            "bgalpha"           => 1,
            "textcolor"         => '#666666',
            "textcolorhigh"     => '#666666',
            "searchword"        => '搜索：输入关键字在店内搜索'
        );
        return json_encode($data);
    }

    /**
     * @return false|mixed|string
     */
    private function defaultMenus()
    {
        $data = [
            [
                "id"        => 'menu_0000000000000',
                "url"       => '',
                "title"     => '顶部菜单一',
                "bgcolor"   => '#fafafa',
                "textcolor" => '#666666',
            ]
        ];
        return json_encode($data);
    }

}
