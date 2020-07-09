<?php

namespace Yunshop\Wechat\admin\menu\controller;

use Illuminate\Support\Facades\DB;
use Yunshop\Wechat\admin\menu\model\Menu;
use Yunshop\Wechat\common\helper\Helper;
use app\common\modules\wechat\WechatApplication;

/**
 *
 */
class DefaultMenuController extends MenuBaseController
{


    public  function test(){
        $data = 'YToxOntzOjY6ImJ1dHRvbiI7YTozOntpOjA7YTozOntzOjQ6Im5hbWUiO3M6Njoi5Zu+5paHIjtzOjQ6InR5cGUiO3M6ODoibWVkaWFfaWQiO3M6ODoibWVkaWFfaWQiO3M6NDM6IjZselptOTM5dVhyR053QVZXT1JTTml5RXRhV24xZjNpaGVDbklGaEdsZnciO31pOjE7YTozOntzOjQ6Im5hbWUiO3M6OToi5YWz6ZSu5a2XIjtzOjQ6InR5cGUiO3M6NToiY2xpY2siO3M6Mzoia2V5IjtzOjE1OiLnvo7jgIHmma/oibLjgIEiO31pOjI7YToyOntzOjQ6Im5hbWUiO3M6MTI6IuiPnOWNleWQjeensCI7czoxMDoic3ViX2J1dHRvbiI7YTo1OntpOjA7YTozOntzOjQ6Im5hbWUiO3M6Njoi5Zu+5paHIjtzOjQ6InR5cGUiO3M6ODoibWVkaWFfaWQiO3M6ODoibWVkaWFfaWQiO3M6NDM6IjZselptOTM5dVhyR053QVZXT1JTTnAzNFlIZUlOdHFoUElkWmRMWTROaTAiO31pOjE7YTozOntzOjQ6Im5hbWUiO3M6Njoi5Zu+54mHIjtzOjQ6InR5cGUiO3M6ODoibWVkaWFfaWQiO3M6ODoibWVkaWFfaWQiO3M6NDM6IjZselptOTM5dVhyR053QVZXT1JTTmpSbDg5UXY5NUdLdXgzOGhYZFgxakUiO31pOjI7YTozOntzOjQ6Im5hbWUiO3M6OToi5YWz6ZSu5a2XIjtzOjQ6InR5cGUiO3M6NToiY2xpY2siO3M6Mzoia2V5IjtzOjY6IuaWsOW5tCI7fWk6MzthOjM6e3M6NDoibmFtZSI7czo2OiLop4bpopEiO3M6NDoidHlwZSI7czo4OiJtZWRpYV9pZCI7czo4OiJtZWRpYV9pZCI7czozOiIxMTEiO31pOjQ7YTozOntzOjQ6Im5hbWUiO3M6NDoidmlldyI7czo0OiJ0eXBlIjtzOjQ6InZpZXciO3M6MzoidXJsIjtzOjE1OiJodHRwOi8vc29zby5jb20iO319fX19';
        dd(Helper::iunserializer(base64_decode($data)));
    }

    public function index()
    {

        $id = request()->id;
        $type = request()->type;
        $currentmenu = $this->currentMenu($id,$type);
        return view('Yunshop\Wechat::admin.menu.defaultmenu', [
        'data'  => json_encode($currentmenu['data'])
        ])->render();
    }


    public function currentMenu($id='',$type=''){
        //add type=1
        if (empty($id) && empty($type)) {
            //获取数据库中当前使用的菜单状态为1
            $button = Menu::getCurrentMenu();
            if($button){
                $button=$button->toArray();
            }else{
                return [];
            }
            $button['data'] = $this->handleDbDate($button);
            $button = $this->addMediaType($button);

            return $button;
        } elseif ($id){
            //修改
            $button = Menu::getMenuByMenuid($id,self::MENU_CURRENTSELF );
            if($button){
                $button =$button->toArray();
            }else{
                return $this->errorJson('error', '菜单不存在或已删除');
            }
            $button['data'] = $this->handleDbDate($button);
            $button=$this->addMediaType($button);
            return $button;
        } else {
            //新增
            return [];
        }

    }


    //发布菜单到微信服务器
    public function pushMenu()
    {
        $post = request()->group;
        $this->checkValue($post);
        $menu = $this->handlePushDate($post);
        $this->releaseMenu($menu['button']);
        $insert = $this->handleDate($post,$menu);
        $insert['status'] = self::STATUS_ON;
        if (empty($post['id'])) {
            //将当前status全部变为零
            $result=DB::transaction(function () use($post,$insert){
                Menu::updataStatus();
                $menuModel =new Menu;
                $menuModel->fill($insert);
                $validate = $menuModel->validator();
                if ($validate->fails()) {
                    return $this->errorJson('error', $validate->messages());
                }
                $result = $menuModel->save();
                return $result;
            });
        } else {
            $result=DB::transaction(function () use($post,$insert){
                Menu::updataStatus();
                $result = Menu::where('id', $post['id'])->update($insert);
                return $result;
            });
        }
        if ($result) {
            return $this->successJson('ok', '发布成功');
        } else {
            return $this->errorJson('error', '发布失败');
        }

    }

    //激活按钮
    public function enableMenu()
    {
       $id = request()->id;
        $button =Menu::getMenuByMenuid($id,self::MENU_CURRENTSELF );
        if($button){
            $button->toArray();
        }else{
            return $this->errorJson('error', '菜单不存在或已删除');
        }
        $button['data'] =$this->handleDbDate($button);
        $menu=$button['data']['button'];
        $this->releaseMenu($menu);
        $result=DB::transaction(function () use($id){
            Menu::updataStatus();
            $result = Menu::where('id', $id)->update(['status'=>1]);
            return $result;
        });
        if ($result) {
            return $this->successJson('ok', '发布成功');
        } else {
            return $this->errorJson('error', '发布失败');
        }

    }
    //保存菜单数据
    public function saveMenu()
    {
        $post = request()->group;
        $this->checkValue($post);
        $menu = $this->handlePushDate($post);
        $insert = $this->handleDate($post,$menu);
        $insert['status']=self::STATUS_OFF;
        if (empty($post['id'])) {
            $menuModel =new Menu;
            $menuModel->fill($insert);
            $validate = $menuModel->validator();
            if ($validate->fails()) {
                return $this->errorJson('error', $validate->messages());
            }
            if($menuModel->save()){
                return $this->successJson('ok', '添加成功');
            }else{
                return $this->errorJson('error', '添加失败');
            }
        } else {
            $result = Menu::where('id',$post['id'])->update($insert);
            if($result){
                return $this->successJson('ok', '更新成功');
            }else{
                return $this->errorJson('error', '更新失败');

            }
        }

    }

    //删除历史菜单菜单
    public function delMenu()
    {
          $id= request()->id;
          $result =Menu::getMenuByMenuid($id,self::MENU_CURRENTSELF );
          if (empty($result)) {
              return $this->errorJson('error', '该菜单信息不存在');
          }
          $result=Menu::deleteMenuById($id);
          if ($result>0) {
              return $this->successJson('ok', '删除成功');
          } else {
              return $this->errorJson('error', '删除失败');
          }
    }


    //点击历史菜单
    public function displayMenu()
    {
        //查数据库将该公众号下状态为0的菜单显示出来
        $button =Menu::getDisplayMenu();
        if(request()->page){
            return json_encode($button);
        }
        return view('Yunshop\Wechat::admin.menu.displaymenu', [
            'data'  => json_encode($button),
        ])->render();
    }




}