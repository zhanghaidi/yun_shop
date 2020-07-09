@extends('layouts.base')
@section('title', "公众号设置")
@section('content')
<style>
    .rightlist #app .rightlist-head{line-height:50px;padding:15px 0;}
    .rightlist #app{margin-left:30px;}
    .el-form-item__label{padding-right:30px;}
    .tip{font-size:12px;color:#999;}
    .rightlist-head-con{padding-right:20px;font-size:16px;color:#888;}
    .el-tag{font-weight:700;font-size:15px;margin-bottom:30px;}
    .el-icon-edit{font-size:16px;padding:0 15px;color:#409EFF;cursor: pointer;}
    /* 滑块选择小白点 */
    .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
    .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
    /* 选择图文 */
    .image_text_head{border:1px solid #dadada;}
    .image_text_head:hover{border:1px #428bca solid;cursor: pointer;color:#428bca; background:#f4f6f9;}

    [v-cloak]{
        display:none;
    }
</style>
<div class="rightlist">
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>

    <div id="app" v-loading="submit_loading" v-cloak>
        <link rel="stylesheet" href="//at.alicdn.com/t/font_913727_z06rk8m5vie.css">
        <div class="rightlist-head">
            <div class="rightlist-head-con">公众号设置</div>
            <!-- <a href="{{ yzWebUrl('plugin.wechat.admin.menu.controller.default-menu.index', array('type' => '1')) }}">
                <el-button type="primary" icon="el-icon-plus">添加新菜单</el-button>
            </a> -->
        </div>
        <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
            <el-tab-pane label="默认菜单" name="default">
                <el-tabs v-model="activeChild" @tab-click="handleClickChild">
                    <el-tab-pane label="默认菜单" name="menu">
                    
                    </el-tab-pane>
                    <el-tab-pane label="历史菜单列表" name="list">
                    历史菜单列表
                    </el-tab-pane>
                </el-tabs>
            </el-tab-pane>
            <el-tab-pane label="个性化菜单" name="individuation">
                <div style="margin-left:30%;padding:30px 0;">
                        <span>菜单组名称：</span>
                        <el-input style="width:30%;" v-model="menu.title" @input="title_tips=false" @blur="menu.title==''?title_tips=true:title_tips=false"></el-input>
                        <p class="menu-tips" style="color:#e15f63" v-show="title_tips">请输入菜单组名称</p>
                    </div>
                    <div style="margin-left:30%;padding:30px 0;">
                        <div style="width:100px;display:inline-block;">菜单显示对象</div>
                        <div style="width:70%;display:inline-block;">
                            <el-select v-model="menu.matchrule.sex" placeholder="请选择性别">
                                <el-option  label="性别不限" value="0"></el-option>
                                <el-option  label="男" value="1"></el-option>
                                <el-option  label="女" value="2"></el-option>
                            </el-select>
                            <el-select v-model="menu.matchrule.client_platform_type" placeholder="请选择手机系统">
                                <el-option  label="手机系统不限" value="0"></el-option>
                                <el-option  label="IOS（苹果）" value="1"></el-option>
                                <el-option  label="Android（安卓）" value="2"></el-option>
                                <el-option  label="Others（其他）" value="3"></el-option>
                            </el-select>
                        </div>
                        <div style="width:70%;display:inline-block;padding-top:15px;">
                            <div style="width:100px;display:inline-block;"></div>
                            <el-select v-model="menu.matchrule.language" placeholder="请选择语言">
                                <el-option  label="语言不限" value="0"></el-option>
                                <el-option v-for="(item,index) in languages" :key="index" :label="item.ch" :value="item.en"></el-option>
                            </el-select>
                                <el-select v-model="menu.matchrule.province" clearable placeholder="请选择省份/直辖市" @change="provinceChange">
                                    <el-option  label="省份/直辖市" value="0"></el-option>
                                    <el-option v-for="(item,index) in aa.abc" :key="item.index" :label="item" :value="index"></el-option>
                                </el-select>
                                <el-select v-model="menu.matchrule.city" v-if="menu.matchrule.province!=0" clearable placeholder="请选择市">
                                    <el-option  label="市" value="0"></el-option>
                                    <el-option v-for="(item,index) in aa[menu.matchrule.province]" :key="item.index" :label="item" :value="item"></el-option>
                                </el-select>
                            
                        </div>
                    </div>
                    
                    <div class="content" style="width:1000px;margin:0 auto;overflow:hidden;">
                        <div>
                            <!-- 预览窗 -->
                            <div class="weixin-preview">
                                <div class="weixin-hd">
                                    <div class="weixin-title">[[weixinTitle]]</div>
                                </div>
                                <div class="weixin-bd">
                                    <ul class="weixin-menu" id="weixin-menu" >
                                        <li v-for="(btn,i) in menu.button" class="menu-item" :class="{current:selectedMenuIndex===i&&selectedMenuLevel()==1}" @click="selectedMenu(i,$event)">
                                            <div class="menu-item-title">
                                                <i class="icon_menu_dot"></i>
                                                <span>[[ btn.name ]]</span>
                                            </div>
                                            <ul class="weixin-sub-menu" v-show="selectedMenuIndex===i">
                                                <li v-for="(sub,i2) in btn.sub_button" class="menu-sub-item" :class="{current:selectedSubMenuIndex===i2&&selectedMenuLevel()==2}"  @click.stop="selectedSubMenu(i2,$event)">
                                                    <div class="menu-item-title">
                                                        <span>[[sub.name]]</span>
                                                    </div>
                                                </li>
                                                <li v-if="btn.sub_button.length<5" class="menu-sub-item" @click.stop="addMenu(2)">
                                                    <div class="menu-item-title">
                                                        <i class="icon14_menu_add"></i>
                                                    </div>
                                                </li>
                                                <i class="menu-arrow arrow_out"></i>
                                                <i class="menu-arrow arrow_in"></i>
                                            </ul>
                                        </li>
                                        <li class="menu-item" v-if="menu.button.length<3" @click="addMenu(1)"> <i class="icon14_menu_add"></i></li>
                                    </ul>
                                </div>
                            </div>
                            <!-- 主菜单 -->
                            <div class="weixin-menu-detail" v-if="selectedMenuLevel()==1">
                                <div class="menu-input-group" style="border-bottom: 2px #e8e8e8 solid;">
                                    <div class="menu-name">[[menu.button[selectedMenuIndex].name]]</div>
                                    <div class="menu-del" @click="delMenu">删除菜单</div>
                                </div>
                                <div class="menu-input-group">
                                    <div class="menu-label">菜单名称</div>
                                    <div class="menu-input">
                                        <input type="text" name="name" placeholder="请输入菜单名称" class="menu-input-text" v-model="menu.button[selectedMenuIndex].name" @input="checkMenuName(menu.button[selectedMenuIndex].name)">
                                        <p class="menu-tips" style="color:#e15f63" v-show="menuNameBounds">字数超过上限</p>
                                        <p class="menu-tips">字数不超过4个汉字或8个字母</p>
                                    </div>
                                </div>
                                <template v-if="menu.button[selectedMenuIndex].sub_button.length==0">
                                    <div class="menu-input-group">
                                        <div class="menu-label" style="height:100px;">菜单内容</div>
                                        <div class="">
                                            <!-- <el-radio v-model="menu.button[selectedMenuIndex].type" label="media_id">发送消息</el-radio> -->
                                            <el-radio v-model="menu.button[selectedMenuIndex].type" label="click">发送消息</el-radio>
                                            <el-radio v-model="menu.button[selectedMenuIndex].type" label="view">跳转网页</el-radio>
                                            <el-radio v-model="menu.button[selectedMenuIndex].type" label="scancode_push">扫码</el-radio>
                                            <el-radio v-model="menu.button[selectedMenuIndex].type" label="miniprogram">关联小程序</el-radio>

                                        </div>
                                        <!-- <div class="menu-input">
                                            <select v-model="menu.button[selectedMenuIndex].type" name="type" class="menu-input-text">
                                                <option value="view">跳转网页(view)</option>
                                                <option value="media_id">发送消息(media_id)</option>
                                                <option value="view_limited">跳转公众号图文消息链接(view_limited)</option>
                                                <option value="miniprogram">打开指定小程序(miniprogram)</option>
                                                <option value="click">自定义点击事件(click)</option>
                                                <option value="scancode_push">扫码上传消息(scancode_push)</option>
                                                <option value="scancode_waitmsg">扫码提示下发(scancode_waitmsg)</option>
                                                <option value="pic_sysphoto">系统相机拍照(pic_sysphoto)</option>
                                                <option value="pic_photo_or_album">弹出拍照或者相册(pic_photo_or_album)</option>
                                                <option value="pic_weixin">弹出微信相册(pic_weixin)</option>
                                                <option value="location_select">弹出地理位置选择器(location_select)</option>
                                            </select>
                                        </div> -->
                                    </div>
                                    <div class="menu-content" v-if="selectedMenuType()==1">
                                        <div class="menu-input-group">
                                            <p class="menu-tips">订阅者点击该菜单会跳到以下链接</p>
                                            <div class="menu-label">页面地址</div>
                                            <div class="menu-input">
                                                <input type="text" placeholder="" class="menu-input-text" v-model="menu.button[selectedMenuIndex].url">
                                                <p class="menu-tips cursor" @click="selectWebsiteUrl(1)">选择地址</p>
                                                <div class="tip">指定点击此菜单时要跳转的链接（注：链接需加http://）</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu-msg-content" v-else-if="selectedMenuType()==2">
                                        <div class="menu-msg-head">发送消息</div>
                                        <div>
                                            <div v-if="menu.button[selectedMenuIndex].type=='click'" style="padding:15px 0 0 50px;">
                                                <div v-if="menu.button[selectedMenuIndex].material.type=='news'">
                                                    <img :src="menu.button[selectedMenuIndex].material.has_many_news[0].thumb_url" style="max-width:50px;" alt="">
                                                    【图文消息】
                                                </div>
                                                <div v-if="menu.button[selectedMenuIndex].material.type=='image'">
                                                    <img :src="menu.button[selectedMenuIndex].material.attachment" style="max-width:50px;" alt="">
                                                    【图片消息】
                                                </div>
                                                <div v-if="menu.button[selectedMenuIndex].material.type=='voice'">
                                                    <i class="iconfont icon-paishipin" style="float:left;display:inline-block;font-size:50px;height:60px;padding-top:20px;"></i>
                                                    <div style="display:block;overflow:hidden;">【语音消息】[[menu.button[selectedMenuIndex].material.media_id]]</div>
                                                </div>
                                                <div v-if="menu.button[selectedMenuIndex].material.type=='video'">
                                                    <i class="iconfont icon-shipindianbo" style="float:left;display:inline-block;font-size:50px;height:60px;padding-top:20px;"></i>
                                                    <div style="display:block;overflow:hidden;">【视频消息】[[menu.button[selectedMenuIndex].material.tag.title]]</div>
                                                </div>
                                            </div>
                                            <div v-if="menu.button[selectedMenuIndex].type=='click'&&menu.button[selectedMenuIndex].key" style="padding:15px 0 0 50px;">
                                                <div>
                                                    【关键字】<span>[[menu.button[selectedMenuIndex].key]]</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="menu-msg-panel">
                                            <div class="menu-msg-select1">
                                                <div class="div1" @click="selectMsgUrl(1)">
                                                    <i class="iconfont icon-haibao" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                    图文
                                                </div>
                                                <div class="div1" @click="selectMsgUrl(2)">
                                                    <i class="iconfont icon-tupian" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                    图片
                                                    
                                                </div>
                                                <div class="div1" @click="selectMsgUrl(3)">
                                                    <i class="iconfont icon-shipindianbo" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                    视频
                                                    </div>

                                                <div class="div1" @click="selectMsgUrl(4)">
                                                    <i class="iconfont icon-paishipin" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                    语音
                                                    </div>
                                                <div class="div1" @click="selectMsgUrl(5)">
                                                    <i class="iconfont icon-gexinghuapingtaitubiao-" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                    触发关键字
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 扫码 -->
                                   
                                    <div class="menu-content" v-else-if="selectedMenuType()==3">
                                        <div class="menu-input-group">
                                            <p class="menu-tips">菜单内容为扫码，那么点击这个菜单是，手机扫描二维码</p>
                                            <div v-if="menu.button[selectedMenuIndex].type=='scancode_push'&&menu.button[selectedMenuIndex].key" style="padding:15px 0 0 50px;">
                                                <div>
                                                    【关键字】<span>[[menu.button[selectedMenuIndex].key]]</span>
                                                </div>
                                            </div>
                                            <div class="menu-msg-panel menu-msg-panel">
                                                <div class="menu-msg-select1">
                                                    <div class="div1" @click="selectMsgUrl(6)">
                                                        <i class="iconfont icon-gexinghuapingtaitubiao-" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                        触发关键字
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu-content" v-else-if="selectedMenuType()==4">
                                        <div class="menu-input-group">
                                            <p class="menu-tips">订阅者点击该子菜单会跳到以下小程序</p>
                                            <div class="menu-label">小程序APPID</div>
                                            <div class="menu-input">
                                                <input type="text" placeholder="请确保小程序与公众号以关联" class="menu-input-text" v-model="menu.button[selectedMenuIndex].appid">
                                            </div>
                                        </div>
                                        <div class="menu-input-group">
                                            <div class="menu-label">页面</div>
                                            <div class="menu-input">
                                                <input type="text" placeholder="请填写跳转页面的小程序访问路径" class="menu-input-text" v-model="menu.button[selectedMenuIndex].pagepath">
                                            </div>
                                        </div>
                                        <div class="menu-input-group">
                                            <div class="menu-label">备用网页</div>
                                            <div class="menu-input">
                                                <input type="text" placeholder="" class="menu-input-text" v-model="menu.button[selectedMenuIndex].url">
                                                <p class="menu-tips">旧版微信客户端无法支持小程序，用户点击菜单时将会打开备用网页。</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <!-- 子菜单 -->
                            <div class="weixin-menu-detail" v-if="selectedMenuLevel()==2">
                                <div class="menu-input-group" style="border-bottom: 2px #e8e8e8 solid;">
                                    <div class="menu-name">[[menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].name]]</div>
                                    <div class="menu-del" @click="delMenu">删除子菜单</div>
                                </div>
                                <div class="menu-input-group">
                                    <div class="menu-label">子菜单名称</div>
                                    <div class="menu-input">
                                        <input type="text" placeholder="请输入子菜单名称" class="menu-input-text" v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].name" @input="checkMenuName(menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].name)">
                                        <p class="menu-tips" style="color:#e15f63" v-show="menuNameBounds">字数超过上限</p>
                                        <p class="menu-tips">字数不超过7个汉字或14个字母</p>
                                    </div>
                                </div>
                                <div class="menu-input-group">
                                    <div class="menu-label" style="padding:30px 0;">子菜单内容</div>
                                    <div class="">
                                        <!-- <el-radio v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type" label="media_id">发送消息</el-radio> -->
                                        <el-radio v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type" label="click">发送消息</el-radio>
                                        <el-radio v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type" label="view">跳转网页</el-radio>
                                        <el-radio v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type" label="scancode_push">扫码</el-radio>
                                        <el-radio v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type" label="miniprogram">关联小程序</el-radio>


                                        <!-- <select v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type" name="type" class="menu-input-text">
                                            <option value="view">跳转网页(view)</option>
                                            <option value="media_id">发送消息(media_id)</option>
                                            <option value="view_limited">跳转公众号图文消息链接(view_limited)</option>
                                            <option value="miniprogram">打开指定小程序(miniprogram)</option>
                                            <option value="click">自定义点击事件(click)</option>
                                            <option value="scancode_push">扫码上传消息(scancode_push)</option>
                                            <option value="scancode_waitmsg">扫码提示下发(scancode_waitmsg)</option>
                                            <option value="pic_sysphoto">系统相机拍照(pic_sysphoto)</option>
                                            <option value="pic_photo_or_album">弹出拍照或者相册(pic_photo_or_album)</option>
                                            <option value="pic_weixin">弹出微信相册(pic_weixin)</option>
                                            <option value="location_select">弹出地理位置选择器(location_select)</option>
                                        </select> -->
                                    </div>
                                </div>
                                <div class="menu-content" v-if="selectedMenuType()==1">
                                    <div class="menu-input-group">
                                        <p class="menu-tips">订阅者点击该子菜单会跳到以下链接</p>
                                        <div class="menu-label">页面地址</div>
                                        <div class="menu-input">
                                            <input type="text" placeholder="" class="menu-input-text" v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].url">
                                            <p class="menu-tips cursor" @click="selectWebsiteUrl()">选择地址</p>
                                            <div class="tip">指定点击此菜单时要跳转的链接（注：链接需加http://）</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="menu-msg-content" v-else-if="selectedMenuType()==2">
                                    <div class="menu-msg-head">发送消息</div>
                                    <div>
                                        <div v-if="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type=='click'" style="padding:15px 0 0 50px;">
                                            <div v-if="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].material.type=='news'">
                                                <img :src="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].material.has_many_news[0].thumb_url" style="max-width:50px;" alt="">
                                                【图文消息】
                                            </div>
                                            <div v-if="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].material.type=='image'">
                                                <img :src="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].material.attachment" style="max-width:50px;" alt="">
                                                【图片消息】
                                            </div>
                                            <div v-if="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].material.type=='voice'">
                                                <i class="iconfont icon-paishipin" style="float:left;display:inline-block;font-size:50px;height:60px;padding-top:20px;"></i>
                                                <div style="display:block;overflow:hidden;">【语音消息】[[menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].material.media_id]]</div>
                                            </div>
                                            <div v-if="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].material.type=='video'">
                                                <i class="iconfont icon-shipindianbo" style="float:left;display:inline-block;font-size:50px;height:60px;padding-top:20px;"></i>
                                                <div style="display:block;overflow:hidden;">【视频消息】[[menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].material.tag.title]]</div>
                                            </div>
                                        </div>
                                        <div v-if="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type=='click'&&menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].key" style="padding:15px 0 0 50px;">
                                            <div>
                                                【关键字】<span>[[menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].key]]</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div>
                                        <div v-if="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].media_id">
                                            这里是发送消息回显内容[[menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].media_id]]
                                        </div>
                                    </div> -->
                                    <div class="menu-msg-panel">
                                        <div class="menu-msg-select1">
                                            <div class="div1" @click="selectMsgUrl(1)">
                                                <i class="iconfont icon-haibao" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                图文
                                            </div>
                                            <div class="div1" @click="selectMsgUrl(2)">
                                                <i class="iconfont icon-tupian" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                图片
                                                
                                            </div>
                                            <div class="div1" @click="selectMsgUrl(3)">
                                                <i class="iconfont icon-shipindianbo" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                视频
                                                </div>

                                            <div class="div1" @click="selectMsgUrl(4)">
                                                <i class="iconfont icon-paishipin" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                语音
                                                </div>
                                            <div class="div1" @click="selectMsgUrl(5)">
                                                <i class="iconfont icon-gexinghuapingtaitubiao-" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                触发关键字
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 扫码 -->
                                <div class="menu-content" v-else-if="selectedMenuType()==3">
                                    <div class="menu-input-group">
                                        <p class="menu-tips">菜单内容为扫码，那么点击这个菜单是，手机扫描二维码</p>
                                        <div v-if="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type=='scancode_push'&&menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].key" style="padding:15px 0 0 50px;">
                                            <div>
                                                【关键字】<span>[[menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].key]]</span>
                                            </div>
                                        </div>
                                        <div class="menu-msg-panel menu-msg-panel">
                                            <div class="menu-msg-select1">
                                                <div class="div1" @click="selectMsgUrl(6)">
                                                    <i class="iconfont icon-gexinghuapingtaitubiao-" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                                                    触发关键字
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="menu-content" v-else-if="selectedMenuType()==4">
                                    <div class="menu-input-group">
                                        <p class="menu-tips">订阅者点击该子菜单会跳到以下小程序</p>
                                        <div class="menu-label">小程序APPID</div>
                                        <div class="menu-input">
                                            <input type="text" placeholder="小程序的appid（仅认证公众号可配置）" class="menu-input-text" v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].appid">
                                        </div>
                                    </div>
                                    <div class="menu-input-group">
                                        <div class="menu-label">小程序路径</div>
                                        <div class="menu-input">
                                            <input type="text" placeholder="小程序的页面路径" class="menu-input-text" v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].pagepath">
                                        </div>
                                    </div>
                                    <div class="menu-input-group">
                                        <div class="menu-label">备用网页</div>
                                        <div class="menu-input">
                                            <input type="text" placeholder="" class="menu-input-text" v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].url">
                                            <p class="menu-tips">旧版微信客户端无法支持小程序，用户点击菜单时将会打开备用网页。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="weixin-btn-group" style="width:100%;">
                            <!-- <el-button id="btn-create" type="primary" v-if="!menu.status==1" @click="submit('save')">仅保存</el-button> -->
                            <el-button id="btn-create" type="success" @click="submit('publish')" v-if="is_look!=1">发布</el-button>
                            <!-- <el-button id="btn-clear" v-if="is_look!=1">清空</el-button> -->
                            <el-button id="btn-clear" @click="back()">返回</el-button>

                        </div>
                    </div>
            </el-tab-pane>
            <!-- 子菜单选择网址弹出框 -->
            <el-dialog title="选择网页链接" :visible.sync="website_url" v-loading="dialog_loading">
                <div v-for="(item,index) in website_url_list" :key="index">
                    <h5 style="border-bottom:1px solid #999">
                        [[item.name]]
                    </h5>
                    <div style="display:inline-block;margin:5px;" v-for="(item1,index1) in item.url_list" :key="index1">
                        <el-button @click="chooseWebsiteUrl(index,index1)" style="width:120px;" >
                            [[item1.name]]
                        </el-button>
                    </div>
                    
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="website_url = false">取 消</el-button>
                </span>
            </el-dialog>
             <!-- 图文选择弹出框 -->
             <el-dialog title="图文" :visible.sync="img_text_url" width="60%" v-loading="dialog_loading">
                <div>
                    <el-tabs v-model="img_text_url0" @tab-click="handleClickImgText">
                        <el-tab-pane label="微信" name="img_text_url1">
                            <el-row>
                                <el-col :span="12">
                                    <el-input placeholder="请输入标题" v-model="search_img_text"></el-input>
                                </el-col>
                                <el-col :span="12">
                                <el-button @click="currentChangeWechatTextImg(1)">搜索</el-button>
                                </el-col>
                            </el-row>
                            <el-row style="overflow-y: scroll;max-height:400px;">
                                <el-col :span="6" v-for="(item,index) in img_text_list" :key="index"  style="margin:10px 10px;width:280px;" @click.native="chooseImgTextUrl(index)">
                                    <div class="image_text_head">
                                        <div style="width:100%;">
                                            <div class="image_text_head_time" style="width:90%;margin-left:5%;border-bottom:1px #dadada solid">
                                                <div style="width:80%;padding:10px 0;">[[item.created_at]]</div>
                                            </div>
                                        </div>
                                        <div style="padding:10px 30px">
                                            <div class="image_text_con" style="min-width:230px;height:180px;overflow:hidden;position:relative;">
                                                <img :src="item.has_many_wechat_news[0].thumb_url"  style="min-width:230px;height:180px;overflow:hidden;" alt="">
                                                <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                    [[item.has_many_wechat_news[0].title]]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <!-- 分页 -->
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChangeWechatTextImg" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                        <el-tab-pane label="本地服务器" name="img_text_url2">
                            <el-row>
                                <el-col :span="12">
                                    <el-input placeholder="请输入标题" v-model="search_img_text"></el-input>
                                </el-col>
                                <el-col :span="12">
                                <el-button @click="currentChangeLocalTextImg(1)">搜索</el-button>
                                </el-col>
                            </el-row>
                            <el-row style="overflow-y: scroll;max-height:400px;">
                                <el-col :span="6" v-for="(item,index) in img_text_list" :key="index"  style="margin:10px 10px;width:280px;" @click.native="chooseImgTextUrl(index)">
                                    <div class="image_text_head">
                                        <div style="width:100%;">
                                            <div class="image_text_head_time" style="width:90%;margin-left:5%;border-bottom:1px #dadada solid">
                                                <div style="width:80%;padding:10px 0;">[[item.created_at]]</div>
                                            </div>
                                        </div>
                                        <div style="padding:10px 30px">
                                            <div class="image_text_con" style="min-width:230px;height:180px;overflow:hidden;position:relative;">
                                                <img :src="item.has_many_wechat_news[0].thumb_url"  style="min-width:230px;height:180px;overflow:hidden;" alt="">
                                                <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                    [[item.has_many_wechat_news[0].title]]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <!-- 分页 -->
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChangeLocalTextImg" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                    </el-tabs>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="img_text_url = false">取 消</el-button>
                    <!-- <el-button type="primary" @click="img_text_url = false">确 定</el-button> -->
                </span>
            </el-dialog>
            <!-- 图片选择弹出框 -->
            <el-dialog title="图片" :visible.sync="img_url" width="60%" v-loading="dialog_loading">
                <div>
                    <el-tabs v-model="img_url0" @tab-click="handleClickImg">
                        <el-tab-pane label="微信" name="img_url1">
                            <el-row style="overflow-y: scroll;max-height:400px;">
                                <el-col :span="5" v-for="(item,index) in img_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseImgUrl(index)">
                                    <div class="image_text_head">
                                        <div style="padding:10px 30px">
                                            <div class="image_text_con" style="min-width:180px;height:150px;overflow:hidden;position:relative;">
                                                <img :src="item.attachment"  style="min-width:180px;height:150px;overflow:hidden;" alt="">
                                                <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                    [[item.filename]]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <!-- 分页 -->
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChangeWechatImg" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                        <el-tab-pane label="本地服务器" name="img_url2">
                            <el-row style="overflow-y: scroll;max-height:400px;">
                                <el-col :span="5" v-for="(item,index) in img_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseImgUrl(index)">
                                    <div class="image_text_head">
                                        <div style="padding:10px 30px">
                                            <div class="image_text_con" style="min-width:180px;height:150px;overflow:hidden;position:relative;">
                                                <img :src="item.attachment"  style="min-width:180px;height:150px;overflow:hidden;" alt="">
                                                <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                    [[item.filename]]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <!-- 分页 -->
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChangeLocalImg" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                        <!-- <el-tab-pane label="本地服务器" name="img_url3">
                        提取网络地址
                        </el-tab-pane> -->
                    </el-tabs>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="img_url = false">取 消</el-button>
                    <!-- <el-button type="primary" @click="img_text_url = false">确 定</el-button> -->
                </span>
            </el-dialog>
            <!-- 视频选择弹出框 -->
            <el-dialog title="视频" :visible.sync="video_url" width="60%" v-loading="dialog_loading">
                <div>
                    <el-tabs v-model="video_url0" @tab-click="handleClickVideo">
                        <el-tab-pane label="微信" name="video_url1">
                            <el-row style="overflow-y: scroll;max-height:400px;">
                                <el-col :span="6" v-for="(item,index) in video_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseVideoUrl(index)">
                                    <div class="image_text_head">
                                        <div style="padding:10px 0px">
                                            <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                                <div style="text-align:center;">
                                                <div class="iconfont icon-shipindianbo" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                    <!-- <img :src="item.img"  style="width:50px;height:50px;" alt=""> -->
                                                    <div>创建于：[[item.createtime]]</div>
                                                </div>
                                                <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                    [[item.filename]]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                             <!-- 分页 -->
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChangeWechatVideo" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                        <el-tab-pane label="本地服务器" name="video_url2">
                            <el-row style="overflow-y: scroll;max-height:400px;">
                                <el-col :span="6" v-for="(item,index) in video_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseVideoUrl(index)">
                                    <div class="image_text_head">
                                        <div style="padding:10px 0px">
                                            <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                                <div style="text-align:center;">
                                                <div class="iconfont icon-shipindianbo" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                    <!-- <img :src="item.img"  style="width:50px;height:50px;" alt=""> -->
                                                    <div>创建于：[[item.created_at]]</div>
                                                </div>
                                                <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                    [[item.filename]]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <!-- 分页 -->
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChangeLocalVideo" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                    </el-tabs>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="video_url = false">取 消</el-button>
                    <!-- <el-button type="primary" @click="img_text_url = false">确 定</el-button> -->
                </span>
            </el-dialog>
            <!-- 语音选择弹出框 -->
            <el-dialog title="语音" :visible.sync="audio_url" width="60%" v-loading="dialog_loading">
                <div>
                    <el-tabs v-model="audio_url0" @tab-click="handleClickAudio">
                        <el-tab-pane label="微信" name="audio_url1">
                            <el-row style="overflow-y: scroll;max-height:400px;">
                                <el-col :span="6" v-for="(item,index) in voice_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseAudioUrl(index)">
                                    <div class="image_text_head">
                                        <div style="padding:10px 0px">
                                            <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                                <div style="text-align:center;">
                                                    <div class="iconfont icon-paishipin" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                    <div>创建于：[[item.created_at]]</div>
                                                </div>
                                                <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                    [[item.filename]]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <!-- 分页 -->
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChangeWechatVoice" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                        <el-tab-pane label="本地服务器" name="audio_url2">
                            <el-row style="overflow-y: scroll;max-height:400px;">
                                <el-col :span="6" v-for="(item,index) in voice_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseAudioUrl(index)">
                                    <div class="image_text_head">
                                        <div style="padding:10px 0px">
                                            <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                                <div style="text-align:center;">
                                                    <div class="iconfont icon-paishipin" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                    <div>创建于：[[item.created_at]]</div>
                                                </div>
                                                <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                    [[item.filename]]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                            <!-- 分页 -->
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChangeLocalVoice" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                    </el-tabs>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="audio_url = false">取 消</el-button>
                    <!-- <el-button type="primary" @click="img_text_url = false">确 定</el-button> -->
                </span>
            </el-dialog>
            <!-- 发送消息-关键字选择弹出框 -->
            <el-dialog title="关键字" :visible.sync="keyword_url" width="60%" v-loading="dialog_loading">
                <el-row>
                    <el-col :span="12">
                        <el-input placeholder="搜索关键字" v-model="search_keyword"></el-input>
                    </el-col>
                    <el-col :span="12">
                    <el-button>搜索</el-button>
                    </el-col>
                </el-row>
                <div>
                    <el-row style="overflow-y: scroll;max-height:400px;">
                        <el-col :span="3" v-for="(item,index) in keyword_list" :key="index"  style="margin:10px 10px;width:150px;overflow:hidden;" @click.native="chooseKeywordUrl(index)">
                            <div class="image_text_head">
                                <div style="text-align:center;">
                                    <div class="image_text_con" style="padding:10px 0">
                                        [[item.content]]
                                    </div>
                                </div>
                            </div>
                        </el-col>
                    </el-row>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="keyword_url = false">取 消</el-button>
                    <!-- <el-button type="primary" @click="img_text_url = false">确 定</el-button> -->
                </span>
            </el-dialog>
             <!-- 扫码-关键字选择弹出框 -->
             <el-dialog title="关键字" :visible.sync="code_keyword_url" width="60%" v-loading="dialog_loading">
                <el-row>
                    <el-col :span="12">
                        <el-input placeholder="搜索关键字" v-model="search_keyword"></el-input>
                    </el-col>
                    <el-col :span="12">
                    <el-button>搜索</el-button>
                    </el-col>
                </el-row>
                <div>
                    <el-row style="overflow-y: scroll;max-height:400px;">
                        <el-col :span="3" v-for="(item,index) in keyword_list" :key="index"  style="margin:10px 10px;width:150px;overflow:hidden;" @click.native="chooseCodeKeywordUrl(index)">
                            <div class="image_text_head">
                                <div style="text-align:center;">
                                    <div class="image_text_con" style="padding:10px 0">
                                        [[item.content]]
                                    </div>
                                </div>
                            </div>
                        </el-col>
                    </el-row>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="code_keyword_url = false">取 消</el-button>
                    <!-- <el-button type="primary" @click="img_text_url = false">确 定</el-button> -->
                </span>
            </el-dialog>
        </el-tabs>

    </div>
    
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            let data = {!! $data?:'{}' !!};
            let languages = {!! $languages?:'{}' !!};
            console.log(languages);
            console.log(data);
            let submit = {!! $submit?:'{}' !!};
            console.log(submit)
            if(data==null){
                data={data:""};
            }
            if(data.data.button){
                for(let i=0;i<data.data.button.length;i++){
                    if(!data.data.button[i].sub_button){
                        data.data.button[i].sub_button=[];
                    }
                    if(data.data.button[i].sub_button.length==0){
                        if(!data.data.button[i].material){
                            data.data.button[i].material="";
                        }
                    }
                    if(data.data.button[i].sub_button.length>0){
                        for(let j=0;j<data.data.button[i].sub_button.length;j++){
                            if(!data.data.button[i].sub_button[j].material){
                                data.data.button[i].sub_button[j].material="";
                            }
                        }
                    }
                }
            }
            
            return{
                submit_loading:false,
                dialog_loading:false,
                languages:languages,//语言组
                title_tips:false,//菜单组输入提示
                weixinTitle: '个性化菜单',
                aa:{
                    abc:{
                        '北京':"北京",'天津':"天津",'河北':"河北",'山西':"山西",'内蒙古':"内蒙古",'辽宁':"辽宁","吉林":"吉林","黑龙江":"黑龙江","上海":"上海","江苏":"江苏","浙江":"浙江","安徽":"安徽","福建":"福建","江西":"江西","山东":"山东","河南":"河南","湖北":"湖北","湖南":"湖南","广东":"广东","广西":"广西","海南":"海南","重庆":"重庆","四川":"四川","贵州":"贵州","云南":"云南","西藏":"西藏","陕西":"陕西","甘肃":"甘肃","青海":"青海","宁夏":"宁夏","新疆":"新疆","台湾":"台湾","香港":"香港","澳门":"澳门","海外":"海外"
                    },
                    '北京':{110101:"东城",110102:"西城",110103:"崇文",110104:"宣武",110105:"朝阳",110106:"丰台",110107:"石景山",110108:"海淀",110109:"门头沟",110111:"房山",110112:"通州",110113:"顺义",110114:"昌平",110115:"大兴",110116:"怀柔",110117:"平谷",110118:"密云",110119:"延庆",110119:"其他"
                    },
                    "天津":{120101:"和平",120102:"河东",120103:"河西",120104:"南开",120105:"河北",120106:"红桥",120107:"塘沽",120108:"汉沽",120109:"大港",120110:"东丽",120111:"西青",120112:"津南",120113:"北辰",120114:"武清",120115:"宝坻",120116:"滨海新",120117:"宁河",120118:"静海",120225:"蓟县",120226:"其他"
                    },
                    '河北':{130100:"石家庄",130200:"唐山市",130300:"秦皇岛",130400:"邯郸",130500:"邢台",130600:"保定",130700:"张家口",130800:"承德",130900:"沧州",131e3:"廊坊",131100:"衡水"
                    },
                    '山西':{140100:"太原",140200:"大同",140300:"阳泉",140400:"长治",140500:"晋城",140600:"朔州",140700:"晋中",140800:"运城",140900:"忻州",141e3:"临汾",141100:"吕梁"
                    },
                    '内蒙古':{150100:"呼和浩特",150200:"包头",150300:"乌海",150400:"赤峰",150500:"通辽",150600:"鄂尔多斯",150700:"呼伦贝尔",150800:"巴彦淖尔",150900:"乌兰察布",152200:"兴安盟",152500:"锡林郭勒盟",152900:"阿拉善盟"
                    },150100:{150102:"新城区",150103:"回民区",150104:"玉泉区",150105:"赛罕区",150121:"土默特左旗",150122:"托克托县",150123:"和林格尔县",150124:"清水河县",150125:"武川县"},150200:{150202:"东河区",150203:"昆都仑区",150204:"青山区",150205:"石拐区",150206:"白云鄂博矿区",150207:"九原区",150221:"土默特右旗",150222:"固阳县",150223:"达尔罕茂明安联合旗"},150300:{150302:"海勃湾区",150303:"海南区",150304:"乌达区"},150400:{150402:"红山区",150403:"元宝山区",150404:"松山区",150421:"阿鲁科尔沁旗",150422:"巴林左旗",150423:"巴林右旗",150424:"林西县",150425:"克什克腾旗",150426:"翁牛特旗",150428:"喀喇沁旗",150429:"宁城县",150430:"敖汉旗"},150500:{150502:"科尔沁区",150521:"科尔沁左翼中旗",150522:"科尔沁左翼后旗",150523:"开鲁县",150524:"库伦旗",150525:"奈曼旗",150526:"扎鲁特旗",150581:"霍林郭勒市"},150600:{150602:"东胜区",150621:"达拉特旗",150622:"准格尔旗",150623:"鄂托克前旗",150624:"鄂托克旗",150625:"杭锦旗",150626:"乌审旗",150627:"伊金霍洛旗"},150700:{150702:"海拉尔区",150703:"扎赉诺尔区",150721:"阿荣旗",150722:"莫力达瓦达斡尔族自治旗",150723:"鄂伦春自治旗",150724:"鄂温克族自治旗",150725:"陈巴尔虎旗",150726:"新巴尔虎左旗",150727:"新巴尔虎右旗",150781:"满洲里市",150782:"牙克石市",150783:"扎兰屯市",150784:"额尔古纳市",150785:"根河市"},150800:{150802:"临河区",150821:"五原县",150822:"磴口县",150823:"乌拉特前旗",150824:"乌拉特中旗",150825:"乌拉特后旗",150826:"杭锦后旗"},150900:{150902:"集宁区",150921:"卓资县",150922:"化德县",150923:"商都县",150924:"兴和县",150925:"凉城县",150926:"察哈尔右翼前旗",150927:"察哈尔右翼中旗",150928:"察哈尔右翼后旗",150929:"四子王旗",150981:"丰镇市"},152200:{152201:"乌兰浩特市",152202:"阿尔山市",152221:"科尔沁右翼前旗",152222:"科尔沁右翼中旗",152223:"扎赉特旗",152224:"突泉县"},152500:{152501:"二连浩特市",152502:"锡林浩特市",152522:"阿巴嘎旗",152523:"苏尼特左旗",152524:"苏尼特右旗",152525:"东乌珠穆沁旗",152526:"西乌珠穆沁旗",152527:"太仆寺旗",152528:"镶黄旗",152529:"正镶白旗",152530:"正蓝旗",152531:"多伦县"},152900:{152921:"阿拉善左旗",152922:"阿拉善右旗",152923:"额济纳旗"},
                    '辽宁':{210100:"沈阳",210200:"大连",210300:"鞍山",210400:"抚顺",210500:"本溪",210600:"丹东",210700:"锦州",210800:"营口",210900:"阜新",211e3:"辽阳",211100:"盘锦",211200:"铁岭",211300:"朝阳",211400:"葫芦岛"
                    },
                    '吉林':{220100:"长春",220200:"吉林",220300:"四平",220400:"辽源",220500:"通化",220600:"白山",220700:"松原",220800:"白城",222400:"延边朝鲜族自治州"
                    },
                    '黑龙江':{230100:"哈尔滨",230200:"齐齐哈尔",230300:"鸡西",230400:"鹤岗",230500:"双鸭山",230600:"大庆",230700:"伊春",230800:"佳木斯",230900:"七台河",231e3:"牡丹江",231100:"黑河",231200:"绥化",232700:"大兴安岭地区"
                    },
                    '上海':{310101:"黄浦",310102:"卢湾",310104:"徐汇",310105:"长宁",310106:"静安",310107:"普陀",310109:"虹口",310110:"杨浦",310112:"闵行",310113:"宝山",310114:"嘉定",310115:"浦东新",310116:"金山",310117:"松江",310118:"青浦",310120:"奉贤",310121:"川沙",310230:"崇明",310231:"其他",
                    },
                    '江苏':{320100:"南京",320200:"无锡",320300:"徐州",320400:"常州",320500:"苏州",320600:"南通",320700:"连云港",320800:"淮安",320900:"盐城",321e3:"扬州",321100:"镇江",321200:"泰州",321300:"宿迁"
                    },
                    '浙江':{330100:"杭州",330200:"宁波",330300:"温州",330400:"嘉兴",330500:"湖州",330600:"绍兴",330700:"金华",330800:"衢州",330900:"舟山",331e3:"台州",331100:"丽水"
                    },
                    '安徽':{340100:"合肥",340200:"芜湖",340300:"蚌埠",340400:"淮南",340500:"马鞍山",340600:"淮北",340700:"铜陵",340800:"安庆",341e3:"黄山",341100:"滁州",341200:"阜阳",341300:"宿州",341500:"六安",341600:"亳州",341700:"池州",341800:"宣城"
                    },
                    '福建':{350100:"福州",350200:"厦门",350300:"莆田",350400:"三明",350500:"泉州",350600:"漳州",350700:"南平",350800:"龙岩",350900:"宁德"
                    },
                    '江西':{360100:"南昌",360200:"景德镇",360300:"萍乡",360400:"九江",360500:"新余",360600:"鹰潭",360700:"赣州",360800:"吉安",360900:"宜春",361e3:"抚州",361100:"上饶"
                    },
                    '山东':{370100:"济南",370200:"青岛",370300:"淄博",370400:"枣庄",370500:"东营",370600:"烟台",370700:"潍坊",370800:"济宁",370900:"泰安",371e3:"威海",371100:"日照",371200:"莱芜",371300:"临沂",371400:"德州",371500:"聊城",371600:"滨州",371700:"菏泽"
                    },
                    '河南':{410100:"郑州",410200:"开封",410300:"洛阳",410400:"平顶山",410500:"安阳",410600:"鹤壁",410700:"新乡",410800:"焦作",410900:"濮阳",411e3:"许昌",411100:"漯河",411200:"三门峡",411300:"南阳",411400:"商丘",411500:"信阳",411600:"周口",411700:"驻马店",419001:"济源"
                    },
                    '湖北':{420100:"武汉",420200:"黄石",420300:"十堰",420500:"宜昌",420600:"襄阳",420700:"鄂州",420800:"荆门",420900:"孝感",421e3:"荆州",421100:"黄冈",421200:"咸宁",421300:"随州",422800:"恩施土家族苗族自治州",429004:"仙桃",429005:"潜江",429006:"天门",429021:"神农架林区"
                    },
                    '湖南':{430100:"长沙",430200:"株洲",430300:"湘潭",430400:"衡阳",430500:"邵阳",430600:"岳阳",430700:"常德",430800:"张家界",430900:"益阳",431e3:"郴州",431100:"永州",431200:"怀化",431300:"娄底",433100:"湘西土家族苗族自治州"
                    },
                    '广东':{440100:"广州",440200:"韶关",440300:"深圳",440400:"珠海",440500:"汕头",440600:"佛山",440700:"江门",440800:"湛江",440900:"茂名",441200:"肇庆",441300:"惠州",441400:"梅州",441500:"汕尾",441600:"河源",441700:"阳江",441800:"清远",441900:"东莞",442e3:"中山",445100:"潮州",445200:"揭阳",445300:"云浮"
                    },
                    '广西':{450100:"南宁",450200:"柳州",450300:"桂林",450400:"梧州",450500:"北海",450600:"防城港",450700:"钦州",450800:"贵港",450900:"玉林",451e3:"百色",451100:"贺州",451200:"河池",451300:"来宾",451400:"崇左"
                    },
                    '海南':{460100:"海口",460200:"三亚",460300:"三沙",460400:"儋州",469001:"五指山",469002:"琼海",469005:"文昌",469006:"万宁",469007:"东方",469021:"定安县",469022:"屯昌县",469023:"澄迈县",469024:"临高县",469025:"白沙黎族自治县",469026:"昌江黎族自治县",469027:"乐东黎族自治县",469028:"陵水黎族自治县",469029:"保亭黎族苗族自治县",469030:"琼中黎族苗族自治县",469031:"西沙群岛",469032:"南沙群岛",469033:"中沙群岛的岛礁及其海域",
                    },
                    '重庆':{500101:"万州",500102:"涪陵",500103:"渝中",500104:"大渡口",500105:"江北",500106:"沙坪坝",500107:"九龙坡",500108:"南岸",500109:"北碚",500110:"万盛",500111:"双桥",500112:"渝北",500113:"巴南",500114:"黔江",500115:"长寿",500116:"綦江",500117:"潼南",500118:"铜梁",500119:"大足",500120:"璧山",500153:"荣昌",500228:"梁平",500229:"城口",500230:"丰都",500231:"垫江",500232:"武隆",500233:"忠县",500234:"开县",500235:"云阳",500236:"奉节",500237:"巫山",500238:"巫溪",500240:"石柱",500241:"秀山",500242:"酉阳",500243:"彭水",500244:"江津",500245:"合川",500246:"永川",500247:"南川",500248:"其他"
                },
                    '四川':{510100:"成都",510300:"自贡",510400:"攀枝花",510500:"泸州",510600:"德阳",510700:"绵阳",510800:"广元",510900:"遂宁",511e3:"内江",511100:"乐山",511300:"南充",511400:"眉山",511500:"宜宾",511600:"广安",511700:"达州",511800:"雅安",511900:"巴中",512e3:"资阳",513200:"阿坝藏族羌族自治州",513300:"甘孜藏族自治州",513400:"凉山彝族自治州"
                    },
                    '贵州':{520100:"贵阳",520200:"六盘水",520300:"遵义",520400:"安顺",520500:"毕节地区",520600:"铜仁地区",522300:"黔西南布依族苗族自治州",522600:"黔东南苗族侗族自治州",522700:"黔南布依族苗族自治州"
                    },
                    '云南':{530100:"昆明",530300:"曲靖",530400:"玉溪",530500:"保山",530600:"昭通",530700:"丽江",530800:"普洱",530900:"临沧",532300:"楚雄彝族自治州",532500:"红河哈尼族彝族自治州",532600:"文山壮族苗族自治州",532800:"西双版纳傣族自治州",532900:"大理白族自治州",533100:"德宏傣族景颇族自治州",533300:"怒江傈僳族自治州",533400:"迪庆藏族自治州"
                    },
                    '西藏':{540100:"拉萨",540200:"日喀则地区",540300:"昌都地区",540400:"林芝地区",542200:"山南地区",542400:"那曲地区",542500:"阿里地区"
                    },
                    '陕西':{610100:"西安",610200:"铜川",610300:"宝鸡",610400:"咸阳",610500:"渭南",610600:"延安",610700:"汉中",610800:"榆林",610900:"安康",611e3:"商洛"
                    },
                    '甘肃':{620100:"兰州",620200:"嘉峪关",620300:"金昌",620400:"白银",620500:"天水",620600:"武威",620700:"张掖",620800:"平凉",620900:"酒泉",621e3:"庆阳",621100:"定西",621200:"陇南",622900:"临夏回族自治州",623e3:"甘南藏族自治州"
                    },
                    '青海':{630100:"西宁",630200:"海东地区",632200:"海北藏族自治州",632300:"黄南藏族自治州",632500:"海南藏族自治州",632600:"果洛藏族自治州",632700:"玉树藏族自治州",632800:"海西蒙古族藏族自治州"
                    },
                    '宁夏':{640100:"银川",640200:"石嘴山",640300:"吴忠",640400:"固原",640500:"中卫"
                    },
                    '新疆':{650100:"乌鲁木齐",650200:"克拉玛依",650400:"吐鲁番地区",652200:"哈密地区",652300:"昌吉回族自治州",652700:"博尔塔拉蒙古自治州",652800:"巴音郭楞蒙古自治州",652900:"阿克苏地区",653e3:"克孜勒苏柯尔克孜自治州",653100:"喀什地区",653200:"和田地区",654e3:"伊犁哈萨克自治州",654200:"塔城地区",654300:"阿勒泰地区",659001:"石河子",659002:"阿拉尔",659003:"图木舒克",659004:"五家渠",
                    },
                    
                    '台湾':{710001:"台北",710002:"高雄",710003:"台南",710004:"台中",710005:"金门县",710006:"南投县",710007:"基隆",710008:"新竹",710009:"嘉义",710010:"新北",710011:"宜兰县",710012:"新竹县",710013:"桃园县",710014:"苗栗县",710015:"彰化县",710016:"嘉义县",710017:"云林县",710018:"屏东县",710019:"台东县",710020:"花莲县",710021:"澎湖县"
                    },
                    '香港':{810001:"香港岛",810002:"九龙",810004:"新界"
                    },
                    '澳门':{820001:"澳门半岛",820002:"离岛",

                    },
                    '海外':{
                        830001:"海外"
                    }
                },
                is_look:submit,//是否为查看个性化菜单
                menu:{
                    button:[],
                    title:"",
                    type:3,
                    matchrule:{
                        sex:'0',
                        client_platform_type:"0",
                        // group_id: "0",
                        language: "0",
                        province:"0",
                        city:"0",
                        // ...data.data.matchrule,
                    },
                    ...data.data,
                },
                // menu: {'button': []},//当前菜单
                selectedMenuIndex:'',//当前选中菜单索引
                selectedSubMenuIndex:'',//当前选中子菜单索引
                menuNameBounds:false,//菜单长度是否过长
                website_url:false,//选择网页弹出框控制
                img_text_url:false,//选择图文弹出框控制
                img_text_url0:"img_text_url1",//选择图文tabs
                img_url0:"img_url1",//选择图片tabs
                search_img_text:"",//搜索图文
                img_url:false,//图片
                video_url:false,//视频
                video_url0:"video_url1",//选择视频tabs
                audio_url:false,//语音
                audio_url0:"audio_url1",//选择语音tabs
                keyword_url:false,//关键字
                keyword_url0:"keyword_url1",//选择关键字tabs
                search_keyword:"",//搜索关键字
                code_keyword_url:false,//扫码-关键字
                code_search_keyword:"",//搜索扫码关键字
                material:{
                    title:'',
                    url:'',
                    thumb_url:''
                },
                img_list:"",
                music_list:"",
                voice_list:"",
                video_list:"",
                keyword_list:"",
                img_text_list:[{has_many_wechat_news:[{thumb_url:''}]}],
                activeName:"individuation",
                activeChild:"menu",
                // 网址数据
                website_url_list:[
                    {
                        name:"商城页面链接",
                        url_list:[
                            {id:1,name:"商城首页",url:"{{ yzAppFullUrl('home') }}"},
                            {id:2,name:"分类导航",url:"{{ yzAppFullUrl('category') }}"},
                            {id:3,name:"全部商品",url:" {{ yzAppFullUrl('searchAll') }}"},
                        ],
                    },{
                        name:"会员中心链接",
                        url_list:[
                            {id:1,name:"会员中心",url:"{{ yzAppFullUrl('member') }}"},
                            {id:2,name:"我的订单",url:"{{ yzAppFullUrl('member/orderList/0')}}"},
                            {id:3,name:"购物车",url:"{{ yzAppFullUrl('cart') }}"},
                            {id:4,name:"我的收藏",url:"{{ yzAppFullUrl('member/collection') }}"},
                            {id:5,name:"我的足迹",url:"{{ yzAppFullUrl('member/footprint') }}"},
                            {id:6,name:"评价",url:"{{ yzAppFullUrl('member/myEvaluation') }}"},
                            {id:7,name:"关系",url:"{{ yzAppFullUrl('member/myrelationship') }}"},
                            {id:8,name:"收货地址",url:"{{ yzAppFullUrl('member/address') }}"},
                            {id:9,name:"我的优惠券",url:"{{ yzAppFullUrl('coupon/coupon_index') }}"},
                            {id:10,name:"领券中心",url:"{{ yzAppFullUrl('coupon/coupon_store') }}"},
                            {id:11,name:"积分页面",url:"{{ yzAppFullUrl('member/integral_v2') }}"},
                            {id:12,name:"积分明细",url:"{{ yzAppFullUrl('member/integrallist') }}"},
                            {id:13,name:"余额页面",url:"{{ yzAppFullUrl('member/balance') }}	"},
                            {id:14,name:"余额明细",url:"{{ yzAppFullUrl('member/detailed') }}"},
                        ],
                    },{
                        name:"我的推广链接",
                        url_list:[
                            {id:1,name:"推广中心",url:"{{ yzAppFullUrl('member/extension') }}"},
                            {id:2,name:"收入明细",url:"{{ yzAppFullUrl('member/incomedetails') }}"},
                            {id:3,name:"收入提现",url:"{{ yzAppFullUrl('member/income') }}"},
                            {id:4,name:"提现明细",url:"{{ yzAppFullUrl('member/presentationRecord') }}"},
                           

                        ],
                    },          
                ],
                // 分页
                loading:false,
                table_loading:false,
                total:0,
                per_size:0,
                current_page:0,

                dialogToken:false,
                dialogKey:false,
                rules:{
                    
                },
                
            }
        },
        created() {
            
        },
        methods: {
            provinceChange(){
                this.menu.matchrule.city = "0";
            },
            handleClick(tab, event) {
                this.submit_loading = true;
                if(tab.name=="default"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.index') !!}';
                }
                if(tab.name=="individuation"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.conditional-menu.conditional-menu') !!}';
                }
                console.log(event);
            },
            handleClickChild(tab, event){
                this.submit_loading = true;
                if(tab.name=="menu"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.index') !!}';
                }
                if(tab.name=="list"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.display-menu') !!}';
                }
            },
            
            // 图片弹出框里的tabs
            handleClickImg() {
                var that = this;
                console.log(that.per_page);
                if(that.img_url0 == "img_url1"){
                    that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.get-wechat-image') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log("hahahahah")
                        that.img_list = response.data.data.data;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                    }),function(res){
                        console.log(res);
                        that.dialog_loading = false;
                    };
                }
                else if(that.img_url0 == "img_url2"){
                    that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.get-local-image') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.img_list = response.data.data.data;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                    }),function(res){
                        console.log(res);
                        that.dialog_loading = false;
                    };
                }
            },
            
            // 图文弹出框里的tabs
            handleClickImgText() {
                var that = this;
                this.search_img_text = ''
                if(that.img_text_url0 == "img_text_url1"){
                    that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.get-wechat-news') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.img_text_list = response.data.data.data;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                    }),function(res){
                        console.log(res);
                        that.dialog_loading = false;
                    };
                }
                else if(that.img_text_url0 == "img_text_url2"){
                    that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.get-local-news') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.img_text_list = response.data.data.data;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                    }),function(res){
                        console.log(res);
                        that.dialog_loading = false;
                    };
                }
            },
             // 语音弹出框里的tabs
             handleClickAudio() {
                var that = this;
                if(that.audio_url0 == "audio_url1"){
                    that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.get-wechat-voice') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.voice_list = response.data.data.data;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                    }),function(res){
                        console.log(res);
                        that.dialog_loading = false;
                    };
                }
                else if(that.audio_url0 == "audio_url2"){
                    that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.get-local-voice') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.voice_list = response.data.data.data;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                    }),function(res){
                        console.log(res);
                        that.dialog_loading = false;
                    };
                }
            },
            // 视频弹出框里的tabs
             handleClickVideo() {
                var that = this;
                if(that.video_url0 == "video_url1"){
                    that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.get-wechat-video') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.video_list = response.data.data.data;
                        for(let i=0;i<that.video_list.length;i++){
                            that.video_list[i].createtime = new Date(parseInt(that.video_list[i].createtime) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
                        }
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                    }),function(res){
                        console.log(res);
                        that.dialog_loading = false;
                    };
                }
                else if(that.video_url0 == "video_url2"){
                    that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.get-local-video') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.video_list = response.data.data.data;
                        for(let i=0;i<that.video_list.length;i++){
                            that.video_list[i].createtime = new Date(parseInt(that.video_list[i].createtime) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
                        }
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                    }),function(res){
                        console.log(res);
                        that.dialog_loading = false;
                    };
                }
            },
            // 本地图片分页
            currentChangeLocalImg(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.get-local-image') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.img_list = response.data.data.data;
                    this.per_size = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.current_page = response.data.data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            // 微信图片分页
            currentChangeWechatImg(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.get-wechat-image') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.img_list = response.data.data.data;
                    this.per_size = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.current_page = response.data.data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            // 微信图文分页
            currentChangeWechatTextImg(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.get-wechat-news') !!}',{page:val,filename:this.search_img_text}).then(function (response){
                    console.log(response);
                    this.img_text_list = response.data.data.data;
                    this.per_size = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.current_page = response.data.data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            // 本地图文分页
            currentChangeLocalTextImg(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.get-local-news') !!}',{page:val,filename:this.search_img_text}).then(function (response){
                    console.log(response);
                    this.img_text_list = response.data.data.data;
                    this.per_size = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.current_page = response.data.data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            // 微信语音分页
            currentChangeWechatVoice(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.get-wechat-voice') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.voice_list = response.data.data.data;
                    this.per_size = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.current_page = response.data.data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            // 本地语音分页
            currentChangeLocalVoice(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.get-local-voice') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.voice_list = response.data.data.data;
                    this.per_size = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.current_page = response.data.data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            // 微信视频分页
            currentChangeWechatVideo(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.get-wechat-video') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.video_list = response.data.data.data;
                    this.per_size = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.current_page = response.data.data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            // 本地视频分页
            currentChangeLocalVideo(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.get-local-video') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.video_list = response.data.data.data;
                    this.per_size = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.current_page = response.data.data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            submit(submit_type) {
                this.menu.title = this.menu.title.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '');//菜单组名去除两端空格
                if(this.menu.title==""){
                    this.title_tips = true;
                    return false;
                }
                // 条件筛选格式验证
                if((this.menu.matchrule.sex==""||this.menu.matchrule.sex==0)
                &&(this.menu.matchrule.province==""||this.menu.matchrule.province==0)
                &&(this.menu.matchrule.client_platform_type==""||this.menu.matchrule.client_platform_type==0)
                &&(this.menu.matchrule.language==""||this.menu.matchrule.language==0)){
                    this.$message.error("请选择菜单显示对象！");
                    return false;
                }
                if(this.menu.button.length==0){
                    // this.title_tips = true;
                    this.$message.error("菜单不能为空！");
                    return false;
                }
                // 选择网页格式验证
               var reg = /^http(s)?:\/\/+/;
               for(let i=0;i<this.menu.button.length;i++){
                    if(this.menu.button[i].sub_button.length==0&&this.menu.button[i].type=="view"){
                        if (!reg.test(this.menu.button[i].url)){
                            this.$message.error("主菜单链接格式错误！")
                            return false;
                        }
                    }
                }
                for(let i=0;i<this.menu.button.length;i++){
                    if(this.menu.button[i].sub_button.length>0){
                        for(let j=0;j<this.menu.button[i].sub_button.length;j++){
                            if(this.menu.button[i].sub_button[j].type=="view"){
                                if (!reg.test(this.menu.button[i].sub_button[j].url)){
                                    this.$message.error("子菜单链接格式错误！")
                                    return false;
                                }
                            }
                        }
                    }
                }
               // 选择发送消息格式验证
               for(let i=0;i<this.menu.button.length;i++){
                    if(this.menu.button[i].sub_button.length==0&&(this.menu.button[i].type=="media_id"||this.menu.button[i].type=="click")){
                        if ((this.menu.button[i].media_id==""||!this.menu.button[i].media_id)&&(this.menu.button[i].key==""||!this.menu.button[i].key)){
                            this.$message.error("主菜单未选择发送消息！");
                            return false;
                        }
                    }
                }
                for(let i=0;i<this.menu.button.length;i++){
                    if(this.menu.button[i].sub_button.length>0){
                        for(let j=0;j<this.menu.button[i].sub_button.length;j++){
                            if(this.menu.button[i].sub_button[j].type=="media_id"||this.menu.button[i].sub_button[j].type=="click"){
                                if ((this.menu.button[i].sub_button[j].media_id==""||!this.menu.button[i].sub_button[j].media_id)&&(this.menu.button[i].sub_button[j].key==""||!this.menu.button[i].sub_button[j].key)){
                                    this.$message.error("子菜单未选择发送消息！");
                                    return false;
                                }
                            }
                        }
                    }
                }
                // 选择扫码格式验证
                for(let i=0;i<this.menu.button.length;i++){
                    if(this.menu.button[i].sub_button.length==0&&this.menu.button[i].type=="scancode_push"){
                        if (this.menu.button[i].key==""||!this.menu.button[i].key){
                            this.$message.error("主菜单未选择扫码触发关键字！");
                            return false;
                        }
                    }
                }

                for(let i=0;i<this.menu.button.length;i++){
                    if(this.menu.button[i].sub_button.length>0){
                        for(let j=0;j<this.menu.button[i].sub_button.length;j++){
                            if(this.menu.button[i].sub_button[j].type=="scancode_push"){
                                if (this.menu.button[i].sub_button[j].key==""||!this.menu.button[i].sub_button[j].key){
                                    this.$message.error("子菜单未选择扫码触发关键字！");
                                    return false;
                                }
                            }
                        }
                    }
                }
                // 选择关联小程序格式验证
                for(let i=0;i<this.menu.button.length;i++){
                    if(this.menu.button[i].sub_button.length==0&&this.menu.button[i].type=="miniprogram"){
                        if (this.menu.button[i].appid==""||!this.menu.button[i].appid){
                            this.$message.error("主菜单appid未输入！");
                            return false;
                        }
                        if (this.menu.button[i].pagepath==""||!this.menu.button[i].pagepath){
                            this.$message.error("主菜单pagepath未输入！");
                            return false;
                        }
                        if (this.menu.button[i].url==""||!this.menu.button[i].url){
                            this.$message.error("主菜单url未输入！");
                            return false;
                        }
                        if (!reg.test(this.menu.button[i].url)){
                            this.$message.error("主菜单url格式错误！")
                            return false;
                        }
                    }
                }

                for(let i=0;i<this.menu.button.length;i++){
                    if(this.menu.button[i].sub_button.length>0){
                        for(let j=0;j<this.menu.button[i].sub_button.length;j++){
                            if(this.menu.button[i].sub_button[j].type=="miniprogram"){
                                if (this.menu.button[i].sub_button[j].appid==""||!this.menu.button[i].sub_button[j].appid){
                                    this.$message.error("子菜单appid未输入！");
                                    return false;
                                }
                                if (this.menu.button[i].sub_button[j].pagepath==""||!this.menu.button[i].sub_button[j].pagepath){
                                    this.$message.error("子菜单pagepath未输入！");
                                    return false;
                                }
                                if (this.menu.button[i].sub_button[j].url==""||!this.menu.button[i].sub_button[j].url){
                                    this.$message.error("子菜单url未输入！");
                                    return false;
                                }
                                if (!reg.test(this.menu.button[i].sub_button[j].url)){
                                    this.$message.error("子菜单url格式错误！")
                                    return false;
                                }
                            }
                        }
                    }
                }
                // console.log(this.form);
                // this.$refs[formName].validate((valid) => {
                //     if (valid) {
                        this.submit_loading = true;
                        this.menu.submit_type = submit_type;//保存还是发布，publish发布，save保存
                        // 父级菜单有子孩子数据处理
                        for(let i=0;i<this.menu.button.length;i++){
                            if(this.menu.button[i].sub_button.length>0){
                                this.menu.button[i]={
                                    type:"click",
                                    sub_button:this.menu.button[i].sub_button,
                                    name:this.menu.button[i].name,
                                };
                            }
                        }
                        // 父级菜单没有子孩子数据处理
                        for(let i=0;i<this.menu.button.length;i++){
                            if(this.menu.button[i].sub_button.length==0){
                                // delete(this.menu.button[i].material);
                            }
                        }
                        // 子孩子数据处理
                        for(let i=0;i<this.menu.button.length;i++){
                            if(this.menu.button[i].sub_button.length>0){
                                for(let j=0;j<this.menu.button[i].sub_button.length;j++){
                                    // delete(this.menu.button[i].sub_button[j].material);
                                }
                            }
                        }
                        console.log("11111111111111111111111222222222")
                        console.log(this.menu);
                        if(submit_type=="save"){
                            // this.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.save-menu') !!}",{'group':this.menu}).then(response => {
                            //     if (response.data.result) {
                            //         this.$message({type: 'success',message: '操作成功!'});
                            //         window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.display-menu') !!}';
                            //     } else {
                            //         this.$message({message: response.data.msg,type: 'error'});
                            //         this.submit_loading = false;
                            //         window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.display-menu') !!}';
                            //     }
                            // },response => {
                            //     this.$message({message: response.data.msg,type: 'error'});
                            //     this.submit_loading = false;
                            //     window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.display-menu') !!}';
                            // });

                        }
                        else {
                            this.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.conditional-menu.push-menu') !!}",{'group':this.menu}).then(response => {
                                if (response.data.result) {
                                    this.$message({type: 'success',message: '操作成功!'});
                                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.conditional-menu.conditional-menu') !!}';
                                } else {
                                    this.$message({message: response.data.msg,type: 'error'});
                                    this.submit_loading = false;
                                }
                            },response => {
                                this.$message({message: response.data.msg,type: 'error'});
                                this.submit_loading = false;
                            });
                        }
                    // }
                    // else {
                    //     return false;
                //     }
                // });
            },
            // 选择网页地址
            chooseWebsiteUrl(index,index1){
                console.log(index);
                var that = this;
                if(that.menu.button[that.selectedMenuIndex].sub_button.length == 0){
                    that.menu.button[that.selectedMenuIndex].url = that.website_url_list[index].url_list[index1].url;
                    that.menu.button[that.selectedMenuIndex].url = that.menu.button[that.selectedMenuIndex].url.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '');
                }
                else{
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].url = that.website_url_list[index].url_list[index1].url;
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].url = that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].url.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '');
                }
                that.website_url = false;
            },
            // 选择图文
            chooseImgTextUrl(index){
                var that = this;
                if(that.menu.button[that.selectedMenuIndex].sub_button.length == 0){
                    that.menu.button[that.selectedMenuIndex].media_id = that.img_text_list[index].media_id;
                    that.menu.button[that.selectedMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].key ="";
                    that.menu.button[that.selectedMenuIndex].material={
                      ...that.menu.button[that.selectedMenuIndex].material
                    };
                    that.menu.button[that.selectedMenuIndex].material.type="news";
                    that.menu.button[that.selectedMenuIndex].material.has_many_news=[{thumb_url:that.img_text_list[index].has_many_wechat_news[0].thumb_url}];
                }
                else{
                    console.log("wwwwwwwwwwwwwwwwwwww")

                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id = that.img_text_list[index].media_id;
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key="";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                        ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                    };
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.type="news";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.has_many_news=[{thumb_url:that.img_text_list[index].has_many_wechat_news[0].thumb_url}];
                    // that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.has_many_news[0].thumb_url=that.has_many_wechat_news[index].has_many_wechat_news[0].thumb_url;
                console.log(that.menu)
                }
                that.img_text_url = false;
            },
            // 选择图片
            chooseImgUrl(index){
                var that = this;
                if(that.menu.button[that.selectedMenuIndex].sub_button.length == 0){
                    if(!that.img_list[index].media_id){
                        that.submit_loading=true,
                        that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.local-to-wechat') !!}",{id:that.img_list[index].id}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            that.menu.button[that.selectedMenuIndex].media_id = response.data.data.media_id;
                            that.menu.button[that.selectedMenuIndex].type="click";
                            that.menu.button[that.selectedMenuIndex].key ="";
                            that.menu.button[that.selectedMenuIndex].material={
                            ...that.menu.button[that.selectedMenuIndex].material
                            };
                            that.menu.button[that.selectedMenuIndex].material.type="image";
                            that.menu.button[that.selectedMenuIndex].material.attachment=response.data.data.attachment;
                        }
                        else{
                            that.$message.error(response.data.msg);
                            that.submit_loading = false;
                            return false;
                        }
                        that.submit_loading = false;
                    }),function(res){
                        console.log(res);
                        that.submit_loading = false;
                        return false;
                    };
                    that.img_url = false;
                    return false;
                    }
                    that.menu.button[that.selectedMenuIndex].media_id = that.img_list[index].media_id;
                    that.menu.button[that.selectedMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].key ="";
                    that.menu.button[that.selectedMenuIndex].material={
                      ...that.menu.button[that.selectedMenuIndex].material
                    };
                    that.menu.button[that.selectedMenuIndex].material.type="image";
                    that.menu.button[that.selectedMenuIndex].material.attachment=that.img_list[index].attachment;
                }
                else{
                    if(!that.img_list[index].media_id){
                        that.submit_loading=true,
                        that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.local-to-wechat') !!}",{id:that.img_list[index].id}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id = response.data.data.media_id;
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].type="click";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key="";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                                ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                            };
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.type="image";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.attachment=response.data.data.attachment;
                        }
                        else{
                            that.$message.error(response.data.msg);
                            that.submit_loading = false;
                            return false;
                        }
                        that.submit_loading = false;
                    }),function(res){
                        console.log(res);
                        that.submit_loading = false;
                        return false;
                    };
                    that.img_url = false;
                    return false;
                    }
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id = that.img_list[index].media_id;
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key="";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                        ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                    };
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.type="image";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.attachment=that.img_list[index].attachment;
                }
                that.img_url = false;
            },
            // 选择视频
            chooseVideoUrl(index){
                var that = this;
                if(that.menu.button[that.selectedMenuIndex].sub_button.length == 0){
                    if(!that.video_list[index].media_id){
                        that.submit_loading = true;
                        that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.local-to-wechat') !!}",{id:that.video_list[index].id}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            that.menu.button[that.selectedMenuIndex].media_id = response.data.data.media_id;
                            that.menu.button[that.selectedMenuIndex].type="click";
                            that.menu.button[that.selectedMenuIndex].key ="";
                            that.menu.button[that.selectedMenuIndex].material={
                            ...that.menu.button[that.selectedMenuIndex].material
                            };
                            that.menu.button[that.selectedMenuIndex].material.type="video";
                            that.menu.button[that.selectedMenuIndex].material.tag={title:response.data.data.media_id.filename};

                        }
                        else{
                            that.$message.error(response.data.msg);
                            that.submit_loading = false;
                            return false;
                        }
                        that.submit_loading = false;
                    }),function(res){
                        console.log(res);
                        that.submit_loading = false;
                        return false;
                    };
                    that.video_url = false;
                    return false;
                    }
                    that.menu.button[that.selectedMenuIndex].media_id = that.video_list[index].media_id;
                    that.menu.button[that.selectedMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].key ="";
                    that.menu.button[that.selectedMenuIndex].material={
                      ...that.menu.button[that.selectedMenuIndex].material
                    };
                    that.menu.button[that.selectedMenuIndex].material.type="video";
                    that.menu.button[that.selectedMenuIndex].material.tag={title:that.video_list[index].filename};
                   
                }
                else{
                    if(!that.video_list[index].media_id){
                        that.submit_loading = true;
                        that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.local-to-wechat') !!}",{id:that.video_list[index].id}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id = response.data.data.media_id;
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].type="click";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key="";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                                ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                            };
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.type="video";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.tag={title:response.data.data.filename};

                        }
                        else{
                            that.$message.error(response.data.msg);
                            that.submit_loading = false;
                            return false;
                        }
                        that.submit_loading = false;
                    }),function(res){
                        console.log(res);
                        that.submit_loading = false;
                        return false;
                    };
                    that.video_url = false;
                    return false;
                    }
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id = that.video_list[index].media_id;
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key="";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                        ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                    };
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.type="video";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.tag={title:that.video_list[index].filename};
                    // that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.tag.title=that.video_list[index].filename;
                }
                that.video_url = false;
            },
            // 选择语音
            chooseAudioUrl(index) {
                var that = this;
                if(that.menu.button[that.selectedMenuIndex].sub_button.length == 0){
                    if(!that.voice_list[index].media_id){
                        that.submit_loading = true;
                        that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.local-to-wechat') !!}",{id:that.voice_list[index].id}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            that.menu.button[that.selectedMenuIndex].media_id = response.data.data.media_id;
                            that.menu.button[that.selectedMenuIndex].type="click";
                            that.menu.button[that.selectedMenuIndex].key ="";
                            that.menu.button[that.selectedMenuIndex].material={
                            ...that.menu.button[that.selectedMenuIndex].material
                            };
                            that.menu.button[that.selectedMenuIndex].material.type="voice";
                            that.menu.button[that.selectedMenuIndex].material.media_id=response.data.data.media_id;
                        }
                        else{
                            that.$message.error(response.data.msg);
                            that.submit_loading = false;
                            return false;
                        }
                        that.submit_loading = false;
                    }),function(res){
                        console.log(res);
                        that.submit_loading = false;
                        return false;
                    };
                    that.audio_url = false;
                    return false;
                    }
                    that.menu.button[that.selectedMenuIndex].media_id = that.voice_list[index].media_id;
                    that.menu.button[that.selectedMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].key ="";
                    that.menu.button[that.selectedMenuIndex].material={
                      ...that.menu.button[that.selectedMenuIndex].material
                    };
                    that.menu.button[that.selectedMenuIndex].material.type="voice";
                    that.menu.button[that.selectedMenuIndex].material.media_id=that.voice_list[index].media_id;
                }
                else{
                    if(!that.voice_list[index].media_id){
                        that.submit_loading = true;
                        that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.local-to-wechat') !!}",{id:that.voice_list[index].id}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id = response.data.data.media_id;
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].type="click";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key="";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                                ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                            };
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.type="voice";
                            that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.media_id=response.data.data.media_id;
                        }
                        else{
                            that.$message.error(response.data.msg);
                            that.submit_loading = false;
                            return false;
                        }
                        that.submit_loading = false;
                    }),function(res){
                        console.log(res);
                        that.submit_loading = false;
                        return false;
                    };
                    that.audio_url = false;
                    return false;
                    }
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id = that.voice_list[index].media_id;
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key="";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                        ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                    };
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.type="voice";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material.media_id=that.voice_list[index].media_id;
                }
                that.audio_url = false;
            },
            // 发送消息-选择关键字
            chooseKeywordUrl(index) {
                var that = this;
                if(that.menu.button[that.selectedMenuIndex].sub_button.length == 0){
                    that.menu.button[that.selectedMenuIndex].key = that.keyword_list[index].content;
                    that.menu.button[that.selectedMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].media_id="";
                    that.menu.button[that.selectedMenuIndex].material={
                      ...that.menu.button[that.selectedMenuIndex].material
                    };
                    
                }
                else{
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key = that.keyword_list[index].content;
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].type="click";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id="";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                        ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                    };
                }
                that.keyword_url = false;

            },
            // 扫码-选择关键字
            chooseCodeKeywordUrl(index) {
                var that = this;
                if(that.menu.button[that.selectedMenuIndex].sub_button.length == 0){
                    that.menu.button[that.selectedMenuIndex].key = that.keyword_list[index].content;
                    that.menu.button[that.selectedMenuIndex].media_id="";
                    that.menu.button[that.selectedMenuIndex].material={
                      ...that.menu.button[that.selectedMenuIndex].material
                    };
                }
                else{
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].key = that.keyword_list[index].content;
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].media_id="";
                    that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material={
                        ...that.menu.button[that.selectedMenuIndex].sub_button[that.selectedSubMenuIndex].material
                    };
                }
                that.code_keyword_url = false;
            },
        //选中主菜单
        selectedMenu:function(i,event){
            this.selectedSubMenuIndex=''
            this.selectedMenuIndex=i
            var selectedMenu=this.menu.button[this.selectedMenuIndex]
            //清空选中media_id 防止再次请求
			
            //检查名称长度
            this.checkMenuName(selectedMenu.name)
        },
        //选中子菜单
        selectedSubMenu:function(i,event){
            this.selectedSubMenuIndex=i
            var selectedSubMenu=this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex]
            
            this.checkMenuName(selectedSubMenu.name)
        },
        //选中菜单级别
		selectedMenuLevel: function () {
            if (this.selectedMenuIndex !== '' && this.selectedSubMenuIndex === '') {
                //主菜单
                return 1;
            }else if (this.selectedMenuIndex !== '' && this.selectedSubMenuIndex !== '') {
                //子菜单
                return 2;
            }else {
                //未选中任何菜单
                return 0;
            }
        },
        //获取菜单类型 1. view网页类型，2. media_id类型和view_limited类型 3. click点击类型，4.miniprogram表示小程序类型
        selectedMenuType: function () {
            if (this.selectedMenuLevel() == 1&&this.menu.button[this.selectedMenuIndex].sub_button.length==0) {
                //主菜单
                switch (this.menu.button[this.selectedMenuIndex].type) {
                    case 'view':return 1;
                    case 'media_id':return 2;
                    case 'click':return 2;
                    case 'view_limited':return 2;
                    case 'click':return 2;
                    case 'scancode_push':return 3;
                    case 'scancode_waitmsg':return 3;
                    case 'pic_sysphoto':return 3;
                    case 'pic_photo_or_album':return 3;
                    case 'pic_weixin':return 3;
                    case 'location_select':return 3;
                    case 'miniprogram':return 4;
                }
            } else if (this.selectedMenuLevel() == 2) {
                //子菜单
                switch (this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].type) {
                    case 'view':return 1;
                    case 'media_id':return 2;
                    case 'click':return 2;
                    case 'view_limited':return 2;
                    case 'click':return 2;
                    case 'scancode_push':return 3;
                    case 'scancode_waitmsg':return 3;
                    case 'pic_sysphoto':return 3;
                    case 'pic_photo_or_album':return 3;
                    case 'pic_weixin':return 3;
                    case 'location_select':return 3;
                    case 'miniprogram':return 4;
                }
            } else {
                return 1;
            }
        },
        //添加菜单
		addMenu:function(level){
			if(level==1&&this.menu.button.length<3){
				this.menu.button.push({
					"type": "view",
					"name": "菜单名称",
					"sub_button": [],
                    "url":"",
                    material:{type:""},
				})
                this.selectedMenuIndex=this.menu.button.length-1
                this.selectedSubMenuIndex=''
                this.checkMenuName(this.menu.button[this.selectedMenuIndex].name)
			}
			if(level==2&&this.menu.button[this.selectedMenuIndex].sub_button.length<5){
				this.menu.button[this.selectedMenuIndex].sub_button.push({
					"type": "view",
					"name": "子菜单名称",
                    "url":"",
                    material:{type:""},
				})
				this.selectedSubMenuIndex=this.menu.button[this.selectedMenuIndex].sub_button.length-1
                this.checkMenuName(this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].name)
			}
            
            
		},
        //删除菜单
		delMenu:function(){
			if(this.selectedMenuLevel()==1&&confirm('删除后菜单下设置的内容将被删除')){
				if(this.selectedMenuIndex===0){
					this.menu.button.splice(this.selectedMenuIndex, 1);
					this.selectedMenuIndex = 0;
				}else{
					this.menu.button.splice(this.selectedMenuIndex, 1);
					this.selectedMenuIndex -=1;
				}
				if(this.menu.button.length==0){
                    this.selectedMenuIndex = ''
                }
			}else if(this.selectedMenuLevel()==2){
                if(this.selectedSubMenuIndex===0){
                    this.menu.button[this.selectedMenuIndex].sub_button.splice(this.selectedSubMenuIndex, 1);
                    this.selectedSubMenuIndex = 0;
                }else{
                    this.menu.button[this.selectedMenuIndex].sub_button.splice(this.selectedSubMenuIndex, 1);
                    this.selectedSubMenuIndex -= 1;
                }
                if(this.menu.button[this.selectedMenuIndex].sub_button.length==0){
                    this.selectedSubMenuIndex = ''
                }
			}
		},
        // 返回上一页
        back(){
            history.back(-1);
        },
        //检查菜单名称长度
		checkMenuName:function(val){
			if(this.selectedMenuLevel()==1&&this.getMenuNameLen(val)<=8){
                this.menuNameBounds=false
			}else if(this.selectedMenuLevel()==2&&this.getMenuNameLen(val)<=14){
                this.menuNameBounds=false
			}else{
			    this.menuNameBounds=true
            }
		},
        //获取菜单名称长度
        getMenuNameLen: function (val) {
            var len = 0;
            for (var i = 0; i < val.length; i++) {
                var a = val.charAt(i);
                a.match(/[^\x00-\xff]/ig) != null?len += 2:len += 1;
            }
            return len;
        },
        
        //选择网页链接
        selectWebsiteUrl (){
            var that = this;
            that.website_url = true;
            // that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.save-menu') !!}",{'group':this.menu}).then(response => {
            //     if (response.data.result) {
            //         that.$message({type: 'success',message: '操作成功!'});
            //         window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.display-menu') !!}';
            //     } else {
            //         that.$message({message: response.data.msg,type: 'error'});
            //         that.submit_loading = false;
            //     }
            // },response => {
            //     that.$message({message: response.data.msg,type: 'error'});
            //     that.submit_loading = false;
            // });

            console.log("选择地址！！！");
        },
        // 选择发送消息
        selectMsgUrl(x) {
            console.log("选择发送消息！！！");
            var that = this;
            that.dialog_loading = true;
            if(x===1) {
                that.img_text_url = true;
                that.img_text_url0 = "img_text_url1";
                that.handleClickImgText();
            }
            if(x===2) {
                that.img_url = true;
                that.img_url0 = "img_url1";
                that.handleClickImg();
            }
            if(x===3) {
                that.video_url = true;
                that.video_url0="video_url1";
                that.handleClickVideo();
                
            }
            if(x===4) {
                that.audio_url = true;
                that.audio_url0="audio_url1";
                that.handleClickAudio();
            }
            if(x===5) {
                that.keyword_url = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.get-keywords') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log("hahahahah")
                        that.keyword_list = response.data.data;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                }),function(res){
                    console.log(res);
                    that.dialog_loading = false;
                };
                
            }
            if(x===6) {
                that.code_keyword_url = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.get-keywords') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log("hahahahah")
                        that.keyword_list = response.data.data;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                }),function(res){
                    console.log(res);
                    that.dialog_loading = false;
                };
                
               
            }
        },
		
        },
    })

</script>
<style>
    *{
        box-sizing: border-box;
    }
    ul{
        padding:0;
    }
    li{
        list-style:none;
    }
    #app-menu{
        overflow: hidden;
        width:950px;
    }
    .weixin-preview{
        position: relative;
        display:inline-block;
        width: 320px;
        height: 540px;
        float: left;
        margin-right:10px;
        border: 1px solid #e7e7eb;
    }
    .weixin-preview a{
        text-decoration: none;
        color: #616161;
    }
    .weixin-preview .weixin-hd{
        color: #fff;
        text-align: center;
        position: relative;
        top:0px;
        left:0px;
        width: 320px;
        height:64px;
        background: transparent url({{ plugin_assets('wechat', 'assets/images/menu_head.png') }}) no-repeat 0 0;
        background-position: 0 0;
    }
    .weixin-preview .weixin-hd .weixin-title{
        color:#fff;
        font-size:15px;
        width:100%;
        text-align: center;
        position:absolute;
        top: 33px;
        left: 0px;
    }

    .weixin-preview .weixin-bd{
        
    }
    .weixin-preview .weixin-menu{
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        border-top: 1px solid #e7e7e7;
        background: transparent url({{ plugin_assets('wechat', 'assets/images/menu_foot.png') }}) no-repeat 0 0;
        background-position: 0 0;
        background-repeat: no-repeat;
        padding-left: 43px;
        margin-bottom:0px;
    }
    /*一级*/
    .weixin-preview .weixin-menu .menu-item{
        position: relative;
        float: left;
        line-height: 50px;
        height:50px;
        text-align: center;
        width:33.33%;
        border-left: 1px solid #e7e7e7;
        cursor: pointer;
        color:#616161;
    }
    .weixin-preview .weixin-menu .menu-item:first-child{
        /*border-left-width:0px;*/
    }

    /*二级*/
    .weixin-preview .weixin-sub-menu{
        position: absolute;
        bottom: 60px;
        left: 0;
        right: 0;
        border-top: 1px solid #d0d0d0;
        margin-bottom:0px;
        background: #fafafa;
        display: block;
        padding:0;
    }
    .weixin-preview .weixin-sub-menu .menu-sub-item{
        line-height: 50px;
        height:50px;
        text-align: center;
        width:100%;
        border: 1px solid #d0d0d0;
        border-top-width: 0px;
        cursor: pointer;
        position: relative;
        color:#616161;
    }
    .weixin-preview .menu-arrow{
        position: absolute;
        left: 50%;
        margin-left: -6px;
    }
    .weixin-preview .arrow_in{
        bottom: -4px;
        display: inline-block;
        width: 0px;
        height: 0px;
        border-width: 6px 6px 0px;
        border-style: solid dashed dashed;
        border-color: #fafafa  transparent transparent;
    }
    .weixin-preview .arrow_out{
        bottom: -5px;
        display: inline-block;
        width: 0px;
        height: 0px;
        border-width: 6px 6px 0px;
        border-style: solid dashed dashed;
        border-color: #d0d0d0 transparent transparent;
    }

    .weixin-preview .menu-item .menu-item-title,.weixin-preview .menu-sub-item .menu-item-title{
        width:100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        box-sizing: border-box;
    }


    .weixin-preview .menu-item.current,.weixin-preview .menu-sub-item.current{
        border: 1px solid #44b549;
        background: #fff;
        color:#44b549;
    }
    .weixin-preview .weixin-sub-menu.show{
        display:block;
    }
    .weixin-preview .icon_menu_dot{
        background: url({{ plugin_assets('wechat', 'assets/images/plus.png') }}) 0px -36px no-repeat;
        width: 7px;
        height: 7px;
        vertical-align: middle;
        display: inline-block;
        margin-right: 2px;
        margin-top: -2px;
    }
    .weixin-preview .icon14_menu_add{
        background: url({{ plugin_assets('wechat', 'assets/images/plus.png') }}) 0px 0px no-repeat;
        width: 14px;
        height: 14px;
        vertical-align: middle;
        display: inline-block;
        margin-top: -2px;
    }
    .weixin-preview li:hover .icon14_menu_add{
        background: url({{ plugin_assets('wechat', 'assets/images/plus.png') }}) 0px -18px no-repeat;
    }

    .weixin-preview .menu-item:hover{
        color: #000;
    }
    .weixin-preview .menu-sub-item:hover{
        background: #eee;
    }

    .weixin-preview li.current:hover{
        background: #fff;
        color: #44b549;
    }

    /*菜单内容*/
    .weixin-menu-detail{
        display:inline-block;
        width: 600px;
        padding: 0px 20px 5px;
        background-color: #f4f5f9;
        border: 1px solid #e7e7eb;
        float: left;
        min-height: 540px;
        overflow:hidden;
    }
    .weixin-menu-detail .menu-name{
        float: left;
        height: 40px;
        line-height: 40px;
        font-size: 18px;
    }
    .weixin-menu-detail .menu-del{
        float: right;
        height: 40px;
        line-height: 40px;
        color: #459ae9;
        cursor:pointer;
    }
    .weixin-menu-detail .menu-input-group{
        width:540px;
        margin:10px 0 30px 0;
        overflow: hidden;
    }
    .weixin-menu-detail .menu-label{
        float: left;
        height: 30px;
        line-height: 30px;
        width:80px;
        text-align: right;
    }
    .weixin-menu-detail .menu-input{
        float:left;
        width: 380px
    }
    .weixin-menu-detail .menu-input-text{
        border: 0px;
        outline: 0px;
        background: #fff;
        width: 300px;
        padding: 5px 0px 5px 0px;
        margin-left: 10px;
        text-indent: 10px;
        height: 35px;
    }
    .weixin-menu-detail .menu-tips{
        color: #8d8d8d;
        padding-top: 4px;
        margin:0;
    }
    .weixin-menu-detail .menu-tips.cursor{
        color: #459ae9;
        cursor: pointer;
    }

    .weixin-menu-detail .menu-input .menu-tips{
        margin:0 0 0 10px;
    }
    .weixin-menu-detail .menu-content{
        padding: 16px 20px;
        border: 1px solid #e7e7eb;
        background-color: #fff;
    }
    .weixin-menu-detail .menu-content .menu-input-group{
        margin: 0px 0 10px 0;
    }
    .weixin-menu-detail .menu-content .menu-label{
        text-align: left;
        width:100px;
    }
    .weixin-menu-detail .menu-content .menu-input-text{
        border: 1px solid #e7e7eb;
    }
    .weixin-menu-detail .menu-content .menu-tips{
        padding-bottom: 10px;
    }

    .weixin-menu-detail .menu-msg-content{
        padding: 0;
        border: 1px solid #e7e7eb;
        background-color: #fff;
    }
    .weixin-menu-detail .menu-msg-content .menu-msg-head{
        overflow: hidden;
        border-bottom: 1px solid #e7e7eb;
        line-height: 38px;
        height: 38px;
        padding: 0 20px;
    }
    .weixin-menu-detail .menu-msg-content .menu-msg-panel{
        padding: 30px 50px;
    }
    .weixin-menu-detail .menu-msg-content .menu-msg-select{
        padding: 40px 20px;
        border: 2px dotted #d9dadc;
        text-align: center;
    }
    .weixin-menu-detail .menu-msg-content .menu-msg-select1{
        padding: 40px 0px;
        border: 2px dotted #d9dadc;
    }
    .weixin-menu-detail .menu-msg-content .menu-msg-select1 .div1{
        display:inline-block;
        width:22%;
        height:100px;
        text-align:center;
        /* line-height:100px; */
        padding-top:30px;
        border:1px #dadada solid;
        margin:10px;
        text-align: center;
        
    }
    .weixin-menu-detail .menu-msg-content .menu-msg-select:hover{
        border-color: #b3b3b3;
    }
    .menu-msg-select1 .div1:hover{
        background:#f4f6f9;
        color:#428bca;
        cursor:pointer;
    }
    .menu-msg-select1{
        padding: 40px 0px;
        border: 2px dotted #d9dadc;
    }
    .menu-msg-select1 .div1{
        display:inline-block;
        width:22%;
        height:100px;
        text-align:center;
        /* line-height:100px; */
        padding-top:30px;
        border:1px #dadada solid;
        margin:10px;
        text-align: center;
        
    }
     .menu-msg-select:hover{
        border-color: #b3b3b3;
    }
    .menu-msg-select1 .div1:hover{
        background:#f4f6f9;
        color:#428bca;
        cursor:pointer;
    }
   
    .weixin-menu-detail .menu-msg-content strong{
        display: block;
        padding-top: 3px;
        font-weight: 400;
        font-style: normal;
    }
    .weixin-menu-detail .menu-msg-content .menu-msg-title{
        float: left;
        width: 310px;
        height: 30px;
        line-height: 30px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .icon36_common{
        width: 36px;
        height: 36px;
        vertical-align: middle;
        display: inline-block;
    }
    .icon36_common.add_gray{
        background: url(../images/base_z381ecd.png) 0 -2548px no-repeat;
    }
    .icon_msg_sender{
        margin-right: 3px;
        margin-top: -2px;
        width: 20px;
        height: 20px;
        vertical-align: middle;
        display: inline-block;
        background: url(../images/msg_tab_z25df2d.png) 0 -270px no-repeat;
    }

    .weixin-btn-group{
        text-align: center;
        width:950px;
        margin:30px 0px;
        overflow: hidden;
    }
    .weixin-btn-group .btn{
        width: 100px;
        border-radius: 0px;
    }

    #material-list{
        padding:20px;
        overflow-y:scroll;
        height: 558px;
    }
    #news-list{
        padding:20px;
        overflow-y:scroll;
        height: 558px;
    }
    #material-list table{
        width:100%;
    }
    .el-radio + .el-radio {
        margin-left: 0px;
    }
    /* .el-radio {
        margin-left: 30px;
    } */

</style>
@endsection
