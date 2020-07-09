<?php

namespace Yunshop\Wechat\admin\menu\controller;

use Illuminate\Support\Facades\DB;
use PhpXmlRpc\Request;
use Yunshop\Wechat\admin\menu\model\Menu;
use app\common\modules\wechat\WechatApplication;
use app\common\helpers\PaginationHelper;
use Yunshop\Wechat\common\helper\Helper;




/**
* 
*/
class ConditionalMenuController extends MenuBaseController
{

    public function index()
    {
        $submit = request()->submit;
        if(request()->id ){
            $id = request()->id ;
            $button = $this->displayMenuDate($id);
        }
        $languages = Helper::menu_languages();
        return view('Yunshop\Wechat::admin.menu.conditionalmenu', [
            'data'   =>  json_encode($button),
            'languages' =>json_encode($languages),
            'submit' =>  $submit
        ])->render();

    }

    //显示菜单的数据
    public function displayMenuDate($id){
        $button =Menu::getMenuByMenuid($id,self::MENU_CONDITIONAL);
        if($button){
            $button = $button->toArray();
        }else{
            return $this->errorJson('error', '菜单不存在或已删除');
        }
        $button['data'] = $this->handleDbDate($button);
        $button = $this->addMediaType($button);
        return $button;
    }

    public function conditionalMenu(){
        //获取表中类型为 type=3的数据
        $button = Menu::getCurrentConditionMenu();
        if(request()->page){
            return json_encode($button);
        }
        return view('Yunshop\Wechat::admin.menu.conditionaldisplaymenu', [
            'data'      => json_encode($button),
        ])->render();
    }

    //  获取用户标签
    public function getFansgroup(){

        $wechatApp = new WechatApplication();
        $tag = $wechatApp->user_tag->lists(); // $user['user_tag']
        return $tag;

    }

    public function pushMenu()
    {
        $post = request()->group;
        $id   = $post['id'];
        $menu = $this->handlePushDate($post,true);
        $res = $this->releaseMenu($menu['button'],$menu['matchrule']);
        $menuid = $res->menuid;
        $insert = $this->handleDate($post,$menu,$menuid);
        $insert['status'] = self::STATUS_ON;
        if(empty($id)){
            $this->checkValue($post);
            $menuModel =new Menu;
            $menuModel->fill($insert);
            $validate = $menuModel->validator();
            if ($validate->fails()) {
                return $this->errorJson('error', $validate->messages());
            }
            $result = $menuModel->save();
        } else {
            $result = Menu::where('id', $id)->update($insert);
        }

        if ($result) {
            return $this->successJson('ok', '发布成功');
        } else {
            return $this->errorJson('error', '发布失败');
        }

    }

    public function enableMenu()
    {

        $id = request()->id;
        $is_open = request()->is_open;
        $button =Menu::getMenuByMenuid($id,self::MENU_CONDITIONAL);
        if($button){
            $button = $button->toArray();
        }else{
            return $this->errorJson('error', '菜单不存在或已删除');
        }
        if($is_open == 1){
            $button['data'] =$this->handleDbDate($button);
            $menu= $this->handleConditional($button['data']);
            $res=$this->releaseMenu($menu['button'],$menu['matchrule']);
            $menuid = $res->menuid;
            if($menuid){
                $result = Menu::where('id', $id)->update([
                    'status'   =>   1,
                    'menuid'   =>   $menuid
                ]);
            }
            if (!empty($result)  && !empty($menuid)) {
                return $this->successJson('ok', '激活成功');
            } else {
                return $this->errorJson('error', '激活失败');
            }
        }else{
            $menuid = $button['menuid'];
            $wechatApp = new WechatApplication();
            try{
                $result = $wechatApp->menu->destroy($menuid);
            }catch (\Exception $e) {
                return $this->errorJson(Helper::getErrorMessage($e->getCode(),$e->getMessage()));
            }
            if($result->errcode == 0){
                $result = Menu::where('id', $id)->update([
                    'status'   =>   0,
                    'menuid'   =>   0
                ]);
            }
            if (!empty($result)) {
                return $this->successJson('ok', '关闭成功');
            } else {
                return $this->errorJson('error', '关闭失败');
            }

        }


    }

    public function handleConditional($menu){

        if(empty($menu['matchrule']['province'])){
            unset($menu['matchrule']['province']);
        }
        if(empty($menu['matchrule']['city'])){
            unset($menu['matchrule']['city']);
        }
        if(empty($menu['matchrule']['sex'])){
            unset($menu['matchrule']['sex']);
        }
        if(empty($menu['matchrule']['client_platform_type'])){
            unset($menu['matchrule']['client_platform_type']);
        }
        if(empty($menu['matchrule']['language'])){
            unset($menu['matchrule']['language']);
        }

        return $menu;
    }



//    删除
    public function delMenu()
    {
        $id = request()->id;
        $menu_id = Menu::getMenuIdsByMenuid($id)->menuid;
        if($menu_id > 0){
            try{
                $wechatApp = new WechatApplication();
                $result = $wechatApp->menu->destroy($menu_id);
            }catch (\Exception $e){
                return $this->errorJson(Helper::getErrorMessage($e->getCode(),$e->getMessage()));
            }

            if($result->errcode == 0) {
                $res = Menu::where('id', $id)->update([
                    'status' => 0,
                    'menuid' => 0
                ]);
            }
        }else{
            $res = Menu::where('id', $id)->update([
                'status' => 0,
                'menuid' => 0
            ]);
        }
        if($res){
            $fruit = Menu::destroy($id);
            if($fruit){
                return $this->successJson('ok','删除成功');
            }
        }


        return $this->errorJson('error','删除失败');
    }

    //复制菜单
    public function copyMenu()
    {
            $id =request()->id;
                //获取菜单
            $menu = Menu::getMenuByMenuid($id,self::MENU_CONDITIONAL);
            if($menu){
                $menu = $menu->toArray();
            } else {
                return $this->errorJson('error', '菜单不存在或已删除');
            }

            if ($menu['type'] != self::MENU_CONDITIONAL) {
                return $this->errorJson('error', '该菜单不能复制');
            }
            unset($menu['id'], $menu['menuid'],$menu['created_at'],$menu['updated_at']);
            $menu['status'] = self::STATUS_OFF;
            $menu['title'] = $menu['title'] . '- 复本';
            $menuModel =new Menu;
            $menuModel->fill($menu);
            $validate = $menuModel->validator();
            if ($validate->fails()) {
                return $this->errorJson('error', $validate->messages());
            }
            if($menuModel->save()){
                $id = $menuModel->id;;
                return $this->successJson('ok',$id);
            } else {
                return $this->errorJson('error', '复制失败');
            }

    }





}