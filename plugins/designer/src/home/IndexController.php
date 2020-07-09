<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/24
 * Time: 下午3:14
 */

namespace Yunshop\Designer\home;



use app\common\components\ApiController;
use PhpParser\Node\Expr\Array_;
use Yunshop\Designer\models\Designer;
use Yunshop\Designer\models\DesignerMenu;
use Yunshop\Designer\services\DesignerService;
use Yunshop\Designer\models\ViewSet;
use Illuminate\Support\Facades\Config;


class IndexController extends ApiController
{

    protected $ignoreAction = ['page', 'menu'];

    protected $publicAction = ['page', 'menu'];
    /**
     * 验证是否有默认装修页面
     * @return \Illuminate\Http\JsonResponse
     */
    public function isHasPage()
    {
        $result = Designer::getDefaultDesigner() ? true : false;

        return $this->successJson('接口对接成功', array('status' => $result));
    }

    public function page()
    {
        $pageId = \YunShop::request()->page_id;
        $page = $pageId ? Designer::getDesignerByPageID($pageId) : Designer::getDefaultDesigner();
        if (!$page) {
            return $this->errorJson('未获取到店铺装修数据');
        }
        $result = (new DesignerService())->getPage($page->toArray());
        //echo '<pre>'; print_r($result); exit;
        return $this->successJson('获取装修页面成功', $result);
    }

    public function menu()
    {
        $menuId = \YunShop::request()->menu_id;
        $menu = $menuId ? DesignerMenu::getMenuById($menuId) : DesignerMenu::getDefaultMenu();
        //echo '<pre>'; print_r($page); exit;

        if ($menu) {
            $result = $this->getMenu($menu->toArray());
            //echo '<pre>'; print_r($result); exit;
            return $this->successJson('自定义菜单获取成功', $result);
        }
        return $this->errorJson('未获取到自定义菜单数据');
    }

    private function getMenu($menu)
    {
        return array(
            'menus'         => json_decode($menu['menus'], true),
            'params'        => json_decode($menu['params'], true)
        );
    }
    //获取所有模板
    public function templateSet($request, $integrated = null)
    {
        
        $sets = ViewSet::uniacid()->select('names', 'type')->get()->toArray();

        if (!$sets) {
            if(is_null($integrated)){
                return $this->errorJson('未获取到模板！');
            }else{
                return show_json(0,'未获取到模板！');
            }
        }

        $member = ViewSet::uniacid()->where('type', 'member')->first();
        $extension = ViewSet::uniacid()->where('type', 'extension')->first();

        $data['member']['name'] =  $member->names;
        $data['extension']['name'] = $extension->names;

        if(is_null($integrated)){
            return $this->successJson('获取成功', $data);
        }else{
            return show_json(1,$data);
        }
    }
}
