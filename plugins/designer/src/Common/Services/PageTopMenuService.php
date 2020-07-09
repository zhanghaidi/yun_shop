<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/31
 * Time: 11:01 AM
 */

namespace Yunshop\Designer\Common\Services;


use Yunshop\Designer\Common\Models\TopMenuModel;

class PageTopMenuService
{
    /**
     * @var int
     */
    private $top_menu_id;


    private $topMenuModel;


    public function getTopMenu($top_menu_id)
    {
        $this->top_menu_id = $top_menu_id;

        return $this->topMenuData();
    }

    private function topMenuData()
    {
        $this->topMenuModel = $this->topMenuModel();
        return [
            'menus' => $this->menus(),
            'params' => $this->params(),
            'isshow' => $this->isShow()
        ];
    }

    private function isShow()
    {
        return $this->topMenuModel ? true : false;
    }

    private function menus()
    {
        return json_decode($this->topMenuModel->menus, true);
    }

    private function params()
    {
        return json_decode($this->topMenuModel->params, true);
    }

    private function topMenuModel()
    {
        return TopMenuModel::find($this->top_menu_id);
    }
}
