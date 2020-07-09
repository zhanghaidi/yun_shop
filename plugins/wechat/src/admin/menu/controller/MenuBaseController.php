<?php

namespace Yunshop\Wechat\admin\menu\controller;
use app\common\components\BaseController;
use app\common\modules\wechat\WechatApplication;
use Yunshop\Wechat\admin\menu\model\Menu;
use Yunshop\Wechat\common\model\WechatAttachment;
use Yunshop\Wechat\common\helper\Helper;

/**
 *
 */
class MenuBaseController extends BaseController{

    const MENU_CURRENTSELF    = 1;
    const MENU_HISTORY        = 2;
    const MENU_CONDITIONAL    = 3;
    const STATUS_OFF          = 0;
    const STATUS_ON           = 1;
    const STATUS_SUCCESS      = 0;

    //发布菜单到微信服务器
    public function releaseMenu($menu,$matchRule=[]){
        $wechatApp = new WechatApplication();
        try{
            if(empty($matchRule)){
                $result = $wechatApp->menu->add($menu);
                return $result;
            } else {
                $result = $wechatApp->menu->add($menu,$matchRule);
                return $result;
            }
        }catch (\Exception $e) {
//            return $this->errorJson($e->getMessage());
            return $this->errorJson(Helper::getErrorMessage($e->getCode(),$e->getMessage()));
        }
    }

    public function isMenuOpen(){
        $wechatApp = new WechatApplication();
        $menus = $wechatApp->menu->current();
        return $menus;
    }

    //增加识别素材类型
    public function addMediaType($button){
        $menu = $button['data']['button'];
        $data=[];
        foreach($menu as $key=>$val){
            if($val['media_id']){
                $material = $this->getMediaType($val['media_id']);
                $val['material'] = $material;
                $val['type'] = 'click';
            }
            if(!empty($val['sub_button'])){
                foreach($val['sub_button'] as $k=>$v){
                    if($v['media_id']){
                        $material = $this->getMediaType($v['media_id']);
                        $v['material'] = $material;
                        $v['type'] = 'click';
                    }
                    $val['sub_button'][$k]=$v;
                }
            }
            $data[$key] = $val;
        }
        $button['data']['button'] = $data;
        return $button;
    }


    //对比media_id 获取类型
    public function getMediaType($media_id){
        $media = WechatAttachment::getWechatAttachmentByMediaId($media_id);
        if($media){
            $media = $media->toArray();
        } else {
            return [];
        }
        if ($media['type'] == WechatAttachment::ATTACHMENT_TYPE_NEWS){
            $media=WechatAttachment::getWechatAttachmentAndNewsByMediaId($media_id);
            if($media){
                $media = $media->toArray();
            }
            return $media;
        }
        return $media;
    }





    //检测数据
    public function checkValue($post)
    {
        
        if(empty(trim($post['title']))){
            return $this->errorJson("请填写菜单组名称！");
        }

        if ($post['type'] == MENU_CONDITIONAL && empty($post['matchrule'])) {
            return $this->errorJson("请选择菜单显示对象！");
        }

        $data=Menu::getMenuGroup();
        if(!$post['id']){
            foreach($data as $v){
                if($post['title']==$v){
                    return $this->errorJson("菜单组名称已存在，请重新命名！！");
                }
            }
        }
      if(empty($post['button'])){
          return $this->errorJson("没有设置菜单");
      }
        foreach($post['button'] as $key=>$val){
            if($val['type'] == 'click'  && empty($val['sub_button']) && empty($val['key']) && empty($val['media_id']) ){
                return $this->errorJson("菜单【".$val['name']."】未设置操作选项");
            }

            if($val['type'] == 'scancode_push' && empty($val['sub_button']) &&empty($val['key']) ){
                return $this->errorJson("菜单【".$val['name']."】未设置操作选项");
            }

            if($val['type'] == 'view' && empty($val['sub_button']) &&empty($val['url']) ){
                return $this->errorJson("菜单【".$val['name']."】未设置跳转页面");
            }

            if($val['type'] == 'miniprogram' && empty($val['sub_button']) &&empty($val['url']) &&empty($val['pagepath'])&&empty($val['appid'])  ){
                return $this->errorJson("菜单【".$v['name']."】未设置选项值");
            }
            //检测二级菜单
            if($val['type'] == 'click' && !empty($val['sub_button'])) {
                foreach ($val['sub_button'] as $k => $v) {
                    if($v['type'] == 'click'  && empty($v['sub_button']) && empty($v['key']) && empty($v['media_id']) ) {
                        return $this->errorJson("菜单【" . $v['name'] . "】未设置操作选项");
                    }
                    if ($v['type'] == 'view'  && empty($v['url'])) {
                        return $this->errorJson("菜单【" . $v['name'] . "】未设置跳转页面");
                    }
                    if($v['type'] == 'scancode_push' && empty($v['sub_button']) &&empty($v['key']) ){
                        return $this->errorJson("菜单【" . $v['name'] . "】未设置操作选项");
                    }
                    if($v['type'] == 'miniprogram' && (empty($v['url']) || empty($v['pagepath']) ||empty($v['appid']))  ){
                        return $this->errorJson("菜单【".$v['name']."】未设置选项值");
                    }
                }
            }
        }
    }



    //前端传过来的数据做处理插入表中
    public function handleDate($post,$menu,$menuid=true)
    {
//        if (!empty($post['matchrule']) && $post['matchrule']['group_id'] != 0 )
//        {
//            $menu['matchrule']['groupid'] = $menu['matchrule']['tag_id'];
//            unset($menu['matchrule']['tag_id']);
//        }
        if(!isset($menu['matchrule']['province'])){
            $menu['matchrule']['province'] =0;
        }
        if(!isset($menu['matchrule']['city'])){
            $menu['matchrule']['city'] =0;
        }
        if(!isset($menu['matchrule']['language'])){
            $menu['matchrule']['language'] =0;
        }
        if(!isset($menu['matchrule']['sex'])){
            $menu['matchrule']['sex'] =0;
        }
        if(!isset($menu['matchrule']['client_platform_type'])){
            $menu['matchrule']['client_platform_type'] =0;

        }
        if(empty($menu['matchrule']['province']) && $menu['matchrule']['city']){
            unset($menu['matchrule']['country']);
        }
        if(!empty($menu['matchrule']['province'])){
            $area = trim($menu['matchrule']['country']) . trim($menu['matchrule']['province']);
            if(!empty($menu['matchrule']['city'])){
                $area = trim($menu['matchrule']['country']) . trim($menu['matchrule']['province']). trim($menu['matchrule']['city']);
            }
        }

        $insert = array(
            'uniacid' => \Yunshop::app()->uniacid,
            'menuid' => $menuid,
            'title' => $post['title'],
            'type' => $post['type']?$post['type']:1,
            'sex' => $menu['matchrule']['sex'],
            'group_id' => isset($menu['matchrule']['tag_id']) ? $menu['matchrule']['tag_id'] : 0,
            'client_platform_type' => $menu['matchrule']['client_platform_type'],
            'area' => isset($area) ? $area : '',
            'data' => base64_encode(Helper::iserializer($menu)),
            'status' => STATUS_ON, //define('STATUS_OFF', 0); define('STATUS_ON', 1); define('STATUS_SUCCESS', 0);
            'createtime' => time(),
        );
        return $insert;

    }

    //从数据库获取出来的菜单数据处理
    public function  handleDbDate($menu)
    {
        $menu['data'] = Helper::iunserializer(base64_decode($menu['data']));
//        dd($menu['data']);
        if (!empty($menu['data']['button'])) {
            foreach ($menu['data']['button'] as &$button) {
                if (!empty($button['url'])) {
                    $button['url'] = preg_replace('/(.*)redirect_uri=(.*)&response_type(.*)wechat_redirect/', '$2', $button['url']);
                }
                if (empty($button['sub_button'])) {
                    $button['sub_button'] = array();

                } else {
                    $button['sub_button'] = !empty($button['sub_button']['list']) ? $button['sub_button']['list'] : $button['sub_button'];
                    foreach ($button['sub_button'] as &$subbutton) {
                        if (!empty($subbutton['url'])) {
                            $subbutton['url'] = preg_replace('/(.*)redirect_uri=(.*)&response_type(.*)wechat_redirect/', '$2', $subbutton['url']);
                        }
                    }
                    unset($subbutton);

                }
            }
            unset($button);
        }
        //设置个性化的信息  添加默认值
//        if (!empty($menu['data']['matchrule']['province'])) {
//            $menu['data']['matchrule']['province'] .= '省';
//        }
//        if (!empty($menu['data']['matchrule']['city'])) {
//            $menu['data']['matchrule']['city'] .= '市';
//        }
    
        if (empty($menu['data']['matchrule']['sex'])) {
            $menu['data']['matchrule']['sex'] = '0';
        }
        if (empty($menu['data']['matchrule']['group_id'])) {
            $menu['data']['matchrule']['group_id'] = '-1';
        }
        if (empty($menu['data']['matchrule']['client_platform_type'])) {
            $menu['data']['matchrule']['client_platform_type'] = '0';
        }
        if (empty($menu['data']['matchrule']['language'])) {
            $menu['data']['matchrule']['language'] = '0';
        }
        if (empty($menu['data']['matchrule']['province'])) {
            $menu['data']['matchrule']['province'] = '0';
        }
        if (empty($menu['data']['matchrule']['city'])) {
            $menu['data']['matchrule']['city'] = '0';
        }

        $params = $menu['data'];
        $params['title'] = $menu['title'];
        $params['type'] = $menu['type'];
        $params['id'] = $menu['id'];
        $params['status'] = $menu['status'];

        return $params;
    }

    //发给微信服务器的数据
    public function handlePushDate($post)
    {
        if (!empty($post['button'])) {
            foreach ($post['button'] as $key => &$button) {
                if (!empty($button['sub_button'])) {
                    unset($subbutton);
                }
            }
            unset($button);
        }
        $menu = $this->createMenuData($post);
        return $menu;

    }




    //菜单的数据处理
    public function createMenuData($data_array, $is_conditional = false) {

        $menu = array();
        if (empty($data_array) || empty($data_array['button']) || !is_array($data_array)) {
            return $menu;
        }
        foreach ($data_array['button'] as $button) {
            $temp = array();
            $temp['name'] =  $button['name'];
            if (empty($button['sub_button'])) {
                $temp['type'] = $button['type'];
                if ($button['type'] == 'view') {
                    $temp['url'] = trim($button['url']);
                } elseif ($button['type'] == 'click') {
                    if (!empty($button['media_id']) && empty($button['key'])) {
                        $temp['media_id'] = $button['media_id'];
                        $temp['type'] = 'media_id';
                    } elseif (empty($button['media_id']) && !empty($button['key'])) {
                        $temp['type'] = 'click';
                        $temp['key'] = $button['key'];
                    }
                } elseif ($button['type'] == 'media_id' || $button['type'] == 'view_limited') {
                    $temp['media_id'] = $button['media_id'];
                } elseif ($button['type'] == 'miniprogram') {
                    $temp['appid'] = trim($button['appid']);
                    $temp['pagepath'] = $button['pagepath'];
                    $temp['url'] = trim($button['url']);
                } else {
                    $temp['key'] = $button['key'];
                }
            } else {
                foreach ($button['sub_button'] as $sub_button) {
                    $sub_temp = array();
                    $sub_temp['name'] =  $sub_button['name'];
                    $sub_temp['type'] = $sub_button['type'];
                    if ($sub_button['type'] == 'view') {
                        $sub_temp['url'] = trim($sub_button['url']);
                    } elseif ($sub_button['type'] == 'click') {
                        if (!empty($sub_button['media_id']) && empty($sub_button['key'])) {
                            $sub_temp['media_id'] = $sub_button['media_id'];
                            $sub_temp['type'] = 'media_id';
                        } elseif (empty($sub_button['media_id']) && !empty($sub_button['key'])) {
                            $sub_temp['type'] = 'click';
                            $sub_temp['key'] = $sub_button['key'];
                        }
                    } elseif ($sub_button['type'] == 'media_id' || $sub_button['type'] == 'view_limited') {
                        $sub_temp['media_id'] = $sub_button['media_id'];
                    } elseif ($sub_button['type'] == 'miniprogram') {
                        $sub_temp['appid'] = trim($sub_button['appid']);
                        $sub_temp['pagepath'] = $sub_button['pagepath'];
                        $sub_temp['url'] = trim($sub_button['url']);
                    } else {
                        $sub_temp['key'] = $sub_button['key'];
                    }
                    $temp['sub_button'][] = $sub_temp;
                }
            }
            $menu['button'][] = $temp;
        }

        if ($is_conditional ==false && empty($data_array['matchrule'])) {
            return $menu;
        }
//        echo '<pre>'; var_dump($data_array);die;

        if($data_array['matchrule']['sex'] > 0) {
            $menu['matchrule']['sex'] = $data_array['matchrule']['sex'];
        }
//        if($data_array['matchrule']['group_id'] != -1) {
//            $menu['matchrule']['tag_id'] = $data_array['matchrule']['group_id'];
//        }
        if($data_array['matchrule']['client_platform_type'] > 0) {
            $menu['matchrule']['client_platform_type'] = $data_array['matchrule']['client_platform_type'];
        }
        if(!empty($data_array['matchrule']['province'])) {
            $menu['matchrule']['country'] = '中国';
            $menu['matchrule']['province'] = $data_array['matchrule']['province'];
            if(!empty($data_array['matchrule']['city'])) {
                $menu['matchrule']['city'] = $data_array['matchrule']['city'];
            }
        }
        if(!empty($data_array['matchrule']['language'])) {
            $inarray = 0;
            $languages = Helper::menu_languages();
            foreach ($languages as $key => $value) {
                if(in_array($data_array['matchrule']['language'], $value, true)) {
                    $inarray = 1;
                    break;
                }
            }
            if($inarray === 1) {
                $menu['matchrule']['language'] = $data_array['matchrule']['language'];
            }
        }

        return $menu;
    }


}