<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-05
 * Time: 15:39
 */

namespace Yunshop\Designer\Backend\Modules\BottomMenu\Controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use Yunshop\Designer\Backend\Models\MenuModel;

class StoreController extends BaseController
{
    /**
     * @var string
     */
    protected $ingress = '';

    /**
     * 保存后跳转URL
     *
     * @var string
     */
    protected $jumpUrl = 'plugin.designer.Backend.Modules.BottomMenu.Controllers.records.index';

    /**
     * 提交数据URL
     *
     * @var string
     */
    protected $storeUrl = 'plugin.designer.Backend.Modules.BottomMenu.Controllers.store.update';


    /**
     * @var MenuModel
     */
    protected $menuModel;


    public function preAction()
    {
        parent::preAction();
        $this->menuModel = $this->menuModel();
    }

    /*
     * 添加、编辑页面
     */
    public function index()
    {
        return view('Yunshop\Designer::bottomMenu.store', $this->resultData());
    }

    /*
     * 数据提交接口
     */
    public function update()
    {
        $menuData = $this->menuData();

        $this->menuModel->fill($menuData);

        //$this->validate();

        $result = $this->menuModel->save();
        if ($result) {
            return $this->successJson();
        }
        return $this->errorJson();
    }

    //请求数据 Data
    private function menuData()
    {
        return array(
            'ingress'   => $this->ingress,
            'menu_name' => request()->menu_name,
            'uniacid'   => \YunShop::app()->uniacid,
            'menus'     => htmlspecialchars_decode(request()->menus),
            'params'    => htmlspecialchars_decode(request()->params),
        );
    }

    /**
     * @return array
     */
    private function resultData()
    {
        return array(
            'menuId'     => $this->menuId(),
            'ingress'    => $this->ingress,
            'jumpUrl'    => yzWebUrl($this->jumpUrl),
            'storeUrl'   => yzWebUrl($this->storeUrl),
            'menuName'   => $this->menuModel->menu_name,
            'menuInfo'   => json_encode($this->menuInfo()),
            'menuParams' => json_encode($this->menuParams()),
        );
    }

    /**
     * @return array
     */
    private function menuInfo()
    {
        $menuInfo = json_decode($this->menuModel->menus, true);

        !$menuInfo && $menuInfo = $this->defaultMenus();

        $menuParams = $this->menuParams();

        foreach ($menuInfo as $key => &$menu) {
            $menu['bgcolor'] = empty($key) ? $menuParams['bgcolorhigh'] : $menuParams['bgcolor'];
            $menu['bordercolor'] = empty($key) ? $menuParams['bordercolorhigh'] : $menuParams['bordercolor'];
            $menu['iconcolor'] = empty($key) ? $menuParams['iconcolorhigh'] : $menuParams['iconcolor'];
            $menu['textcolor'] = empty($key) ? $menuParams['textcolorhigh'] : $menuParams['textcolor'];
        }
        return $menuInfo;

    }

    /**
     * @return array
     */
    private function menuParams()
    {
        $menuParams = json_decode($this->menuModel->params, true);

        !$menuParams && $menuParams = $this->defaultParams();

        return $menuParams;
    }

    /**
     * 默认菜单参数
     *
     * @return array
     */
    private function defaultParams()
    {
        return array(
            "previewbg"       => '#999999',
            "height"          => '49px',
            "textcolor"       => '#666666',
            "textcolorhigh"   => '#666666',
            "iconcolor"       => '#666666',
            "iconcolorhigh"   => '#666666',
            "bgcolor"         => '#fafafa',
            "bgcolorhigh"     => '#fafafa',
            "bordercolor"     => '#bfbfbf',
            "bordercolorhigh" => '#bfbfbf',
            "showtext"        => 1,
            "showborder"      => 1,
            "showicon"        => 1,
            "textcolor2"      => '#666666',
            "bgcolor2"        => '#fafafa',
            "bordercolor2"    => '#bfbfbf',
            "showborder2"     => 1
        );
    }

    /**
     * 默认菜单
     *
     * @return array
     */
    private function defaultMenus()
    {
        return array(
            array(
                "id"       => 1,
                "title"    => '购物中心',
                "icon"     => 'fa fa-list',
                "url"      => '',
                "subMenus" => array(
                    array(
                        'title' => '商城首页',
                        'url'   => '',
                        'id'    => "23333",
                        'hrefChoice'   => '1',
                    )
                )
            )
        );
    }

    /**
     * @return MenuModel
     * @throws ShopException
     */
    private function menuModel()
    {
        $menuId = $this->menuId();

        $menuModel = MenuModel::find($menuId);

        if ($menuId && !$menuModel) {
            throw new ShopException('记录错误，请刷新重试！');
        }

        !$menuModel && $menuModel = new MenuModel();

        return $menuModel;
    }

    /**
     * @return int
     */
    private function menuId()
    {
        return (int)request()->menu_id;
    }

}
