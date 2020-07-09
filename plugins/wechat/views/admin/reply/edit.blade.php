@extends('layouts.base')
@section('title', "关键字自动回复")
@section('content')
<style>
    .rightlist #app .rightlist-head{line-height:50px;padding:15px 0;}
    .rightlist #app{margin-left:30px;}
    .el-form-item__label{padding-right:30px;}
    .tip{font-size:12px;color:#999;}
    .rightlist-head-con{padding-right:20px;font-size:16px;color:#888;}
    /* .rightlist-head-con{float:left;padding-right:20px;font-size:16px;color:#888;} */
    .el-tag{font-weight:700;font-size:15px;margin-bottom:30px;}
    .el-icon-edit{font-size:16px;padding:0 15px;color:#409EFF;cursor: pointer;}
    /* 滑块选择小白点 */
    .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
    .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
    
    .tips{font-size:12px;color:#999;}
    .rightlist-head{line-height:50px;border-bottom:1px solid #ccc;}
    /* 选择图文 */
    .image_text_head{border:1px solid #dadada;}
    .image_text_head:hover{border:1px #428bca solid;cursor: pointer;color:#428bca; background:#f4f6f9;}

    input[type=file] {display: none;}
    
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
        padding-top:30px;
        /* line-height:100px; */
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
    [v-cloak] {
        display: none;
    }
</style>
<div class="rightlist">
    <div id="app" v-loading="submit_loading" v-cloak>
    <link rel="stylesheet" href="//at.alicdn.com/t/font_913727_z06rk8m5vie.css">
        <div class="rightlist-head">
            <div class="rightlist-head-con">新建关键字自动回复</div>
        </div>
        <el-form ref="form" :model="data" :rules="rules" label-width="15%" style="padding-top:15px;">
            <el-form-item label="规则名称" prop="name">
                <el-input v-model="data.name" style="width:70%" ></el-input>
                <div class="tips">您可以给这组触发关键字规则起一个名字, 方便下次修改和查看.</div>
            </el-form-item>
            <el-collapse v-model="activeNames" style="margin:20px 0">
                <el-collapse-item title="展开/收起高级设置" name="1" >
                    <template>
                        <el-form-item label="全局置顶"  prop="is_top">
                            <el-radio v-model.number="data.is_top" :label="1">是</el-radio>
                            <el-radio v-model.number="data.is_top" :label="0">否</el-radio>
                        </el-form-item>
                        <el-form-item label="回复优先级" prop="displayorder" v-if="!data.is_top">
                            <el-input v-model.number="data.displayorder" style="width:70%" ></el-input>
                            <div class="tips">规则优先级，越大则越靠前，最大不得超过254</div>
                        </el-form-item>
                        <el-form-item label="是否开启" prop="status">
                            <el-tooltip :content="data.status?'已开启':'已关闭'" placement="top">
                                <el-switch v-model="data.status" :active-value="1" :inactive-value="0"></el-switch>
                            </el-tooltip>
                            <div class="tips">您可以选择临时禁用这条回复.</div>
                        </el-form-item>
                    </template>
                </el-collapse-item>
            </el-form>
        </el-collapse>

       <template>
       <el-table :data="data.has_many_keywords" style="width: 100%">
            <el-table-column prop="content" label="关键字" align="center"></el-table-column>
            <el-table-column label="触发类型" min-width="150" align="center">
                <template slot-scope="scope">
                    <div v-if="scope.row.type==1">精准触发</div>
                    <div v-if="scope.row.type==2">包含关键字触发</div>
                    <div v-if="scope.row.type==3">正则匹配关键字触发</div>
                </template>
            </el-table-column>
            <el-table-column label="操作" align="center">
                <template slot-scope="scope">
                    <el-button type="danger" @click="delKeyword(scope.$index,scope.row.id)">
                        删除
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-button type="primary" icon="el-icon-plus" @click="dialogTableVisible = true">添加关键字</el-button>
        <!-- <el-row>
            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                <el-pagination layout="prev, pager, next" @current-change="currentChange" :total="page_total" :page-size="page_size" background v-loading="loading"></el-pagination>
            </el-col>
        </el-row> -->
        
            <h5 class="rightlist-head">
                触发后回复内容
            </h5>
            <!-- 选择消息内容回显  -->

            <div style="margin-bottom:30px;">
                <div v-for="(item,index) in data.has_many_basic_reply" style="border-bottom:1px #eee solid;padding:15px 0 10px 0;">
                    <el-row style="width:95%;padding-left:2.5%;">
                        <el-col :span="16" align="left">[[item.content]]</el-col>
                        <el-col :span="8" align="right">
                            <el-button type="primary" @click="editText(index)">编辑</el-button>
                            <el-button type="danger" @click="delText(index)">删除</el-button>
                        </el-col>
                    </el-row>
                </div>
                <div v-for="(item,index) in data.has_many_news_reply" style="border-bottom:1px #eee solid;padding:15px 0 10px 0;">
                    <el-row style="width:95%;padding-left:2.5%;">
                        <el-col :span="16" align="left">
                            <img :src="item.thumb" style="max-width:50px;" alt="">
                            【图文消息】[[item.title]]
                        </el-col>
                        <el-col :span="8" align="right">
                            <el-button type="danger" @click="delTextImg(index)">删除</el-button>
                        </el-col>
                    </el-row>
                </div>
                <div v-for="(item,index) in data.has_many_image_reply" style="border-bottom:1px #eee solid;padding:15px 0 10px 0;">
                    <el-row style="width:95%;padding-left:2.5%;">
                        <el-col :span="16" align="left">
                            <img :src="item.has_one_attachment.attachment" style="max-width:50px;" alt="">
                            【图片】[[item.mediaid]]
                        </el-col>
                        <el-col :span="8" align="right">
                            <el-button type="danger" @click="delImg(index)">删除</el-button>
                        </el-col>
                    </el-row>
                </div>
                <div v-for="(item,index) in data.has_many_video_reply" style="border-bottom:1px #eee solid;padding:15px 0 10px 0;">
                    <el-row style="width:95%;padding-left:2.5%;">
                        <el-col :span="16" align="left">
                            <i class="iconfont icon-shipindianbo" style="float:left;display:inline-block;font-size:80px;height:60px;padding-top:20px;"></i>
                            【视频】[[item.filename]]
                        </el-col>
                        <el-col :span="8" align="right">
                            <!-- <el-button>编辑</el-button> -->
                            <el-button type="danger" @click="delVideo(index)">删除</el-button>
                        </el-col>
                    </el-row>
                </div>
                <div v-for="(item,index) in data.has_many_voice_reply" style="border-bottom:1px #eee solid;padding:15px 0 10px 0;">
                    <el-row style="width:95%;padding-left:2.5%;">
                        <el-col :span="16" align="left">
                            <i class="iconfont icon-paishipin" style="float:left;display:inline-block;font-size:80px;height:60px;padding-top:20px;"></i>
                            【语音】[[item.filename]]
                        </el-col>
                        <el-col :span="8" align="right">
                            <!-- <el-button>编辑</el-button> -->
                            <el-button type="danger" @click="delVoice(index)">删除</el-button>
                        </el-col>
                    </el-row>
                </div>
                <div v-for="(item,index) in data.has_many_music_reply" style="border-bottom:1px #eee solid;padding:15px 0 10px 0;">
                    <el-row style="width:95%;padding-left:2.5%;">
                        <el-col :span="16" align="left">
                            <i class="iconfont icon-icon_zhibo-xian" style="float:left;display:inline-block;font-size:80px;height:60px;padding-top:20px;"></i>
                            【音乐】[[item.title]]<br>
                            [[item.description]]
                        </el-col>
                        <el-col :span="8" align="right">
                            <!-- <el-button>编辑</el-button> -->
                            <el-button type="danger" @click="delMusic(index)">删除</el-button>
                        </el-col>
                    </el-row>
                </div>
            </div>
            <div class="menu-msg-panel">
                <div class="menu-msg-select1">
                    <div class="div1" @click="selectMsgUrl(1)">
                        <i class="iconfont icon-haibao" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                        图文
                    </div>
                    <div class="div1" @click="selectMsgUrl(2)">
                        <i class="iconfont icon-zidingyibiaodan" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                        文字
                    </div>
                    <div class="div1" @click="selectMsgUrl(3)">
                        <i class="iconfont icon-tupian" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                        微信图片
                    </div>
                    <div class="div1" @click="selectMsgUrl(4)">
                        <i class="iconfont icon-icon_zhibo-xian" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                        音乐
                    </div>
                    <div class="div1" @click="selectMsgUrl(5)">
                        <i class="iconfont icon-paishipin" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                        语音
                    </div>
                    <div class="div1" @click="selectMsgUrl(6)">
                        <i class="iconfont icon-shipindianbo" style="line-height:20px;float:left;display:inline-block;font-size:20px;width:100%;text-align:center"></i>
                        视频
                    </div>
                </div>
            </div>
        </template>
        <el-col :span="24" style="text-align:center;margin:20px 0;">
            <el-button type="success" @click="submit('form')">
                保存
            </el-button>
        </el-col>
        
        <!-- 添加关键字弹出框  -->
        <el-dialog title="添加关键字" :visible.sync="dialogTableVisible" v-loading="dialog_loading">
            <el-form>
                <el-form-item label="触发关键字">
                    <el-radio v-model.number="keyword_type" :label="1">精准触发</el-radio>
                    <el-radio v-model.number="keyword_type" :label="2">包含关键字触发</el-radio>
                    <el-radio v-model.number="keyword_type" :label="3">正则匹配关键字触发</el-radio>
                </el-form-item>
                <el-form-item label="" style="padding-left:10%">
                    <el-input style="width:80%" v-model="keyword"></el-input>
                    <!-- <div class="tips" v-show="is_exists">该关键字已存在于 10  规则中.</div> -->
                    <div class="tips" v-if="keyword_type!=3">多个关键字请使用逗号隔开，如天气，今日天气</div>
                    <div class="tips" v-if="keyword_type==3" style="padding:0;margin:0;line-height:20px;">
                        用户进行交谈时，对话内容符合述关键字中定义的模式才会执行这条规则。<br>
                        注意：如果你不明白正则表达式的工作方式，请不要使用正则匹配<br>
                        注意：正则匹配使用MySQL的匹配引擎，请使用MySQL的正则语法<br>
                        示例：<br>
                        ^微信匹配以“微信”开头的语句<br>
                        微信$匹配以“微信”结尾的语句<br>
                        ^微信$匹配等同“微信”的语句<br>
                        微信匹配包含“微信”的语句<br>
                        [0-9.-]匹配所有的数字，句号和减号<br>
                        ^[a-zA-Z_]$所有的字母和下划线<br>
                        ^[ [:alpha:]]{3}$所有的3个字母的单词<br>
                        ^a{4}$aaaa<br>
                        ^a{2,4}$aa，aaa或aaaa<br>
                        ^a{2,}$匹配多于两个a的字符串<br>
                    </div>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dialogTableVisible = false">取 消</el-button>
                <el-button type="primary" @click="chooseKeyword">确 定</el-button>
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
        <!-- 文字弹出框 -->
        <el-dialog title="文字" :visible.sync="text_url" width="60%">
            <div>
               <el-input type="textarea" rows="10" v-model="textarea">
               
               </el-input>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="text_url = false">取 消</el-button>
                <el-button type="primary" @click="chooseText">确 定</el-button>
            </span>
        </el-dialog>
        
        <!-- 音乐弹出框 -->
        <el-dialog title="音乐" :visible.sync="music_url" width="60%" v-loading="dialog_loading">
            <div>
            <el-form ref="music_form" :model="music_form" :rules="rules1" label-width="15%">
                <el-form-item label="音乐标题" prop="title">
                    <el-input v-model="music_form.title" style="width:70%" ></el-input>
                </el-form-item>
                <el-form-item label="选择音乐"  prop="url">
                    <el-input v-model="music_form.url" style="width:60%" disabled></el-input>
                    <el-button @click="handleClickMedia">选择媒体文件</el-button>
                    <div class="tips">选择上传的音频文件或直接输入URL地址，常用格式：mp3</div>
                </el-form-item>
                <el-form-item label="高品质链接" prop="hqurl">
                    <el-input v-model.number="music_form.hqurl" style="width:70%" ></el-input>
                    <div class="tips">没有高品质音乐链接，请留空。高质量音乐链接，WIFI环境优先使用该链接播放音乐</div>
                </el-form-item>
                <el-form-item label="描述" prop="description">
                    <el-input v-model.number="music_form.description" style="width:70%" ></el-input>
                    <div class="tips">描述内容将出现在音乐名称下方，建议控制在20个汉字以内最佳</div>
                </el-form-item>
                
            </el-form>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="music_url = false">取 消</el-button>
                <el-button type="primary" @click="chooseMusicUrl">确 定</el-button>
            </span>
        </el-dialog>
        <!-- 媒体弹出框 -->
        <el-dialog title="音乐" :visible.sync="media_url" width="60%" v-loading="dialog_loading">
            <div>
                <el-tabs v-model="media_url0" @tab-click="handleClickMedia">
                    <el-tab-pane label="微信" name="media_url1">
                        <el-row>
                            <el-col :span="24" align="right" style="margin:20px 0;">
                                <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.upload',['type' => 'wechat']) !!}" accept="audio/*" :show-file-list="false" :on-success="uploadVoiceSuccess" :before-upload="beforeUploadVoice">
                                    <el-button type="primary">上传音频</el-button>
                                </el-upload>
                            </el-col>
                        </el-row>
                        <el-row style="overflow-y: scroll;max-height:400px;">
                            <el-col :span="6" v-for="(item,index) in voice_list" :key="index" style="margin:10px 10px;width:230px;" @click.native="chooseMediaUrl(index)">
                                <div class="image_text_head">
                                    <div style="padding:10px 0px">
                                        <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                            <div style="text-align:center;">
                                                <div class="iconfont icon-paishipin" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                <div>创建于：[[item.created_at]]</div>
                                            </div>
                                            <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;height:32px;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
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
                    <el-tab-pane label="本地服务器" name="media_url2">
                        <el-row>
                            <el-col :span="24" align="right" style="margin:20px 0;">
                                <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.upload',['type' => 'local']) !!}" accept="audio/*" :show-file-list="false" :on-success="uploadVoiceSuccess" :before-upload="beforeUploadVoice">
                                    <el-button type="primary">上传音频</el-button>
                                </el-upload>
                            </el-col>
                        </el-row>
                        <el-row style="overflow-y: scroll;max-height:400px;">
                            <el-col :span="6" v-for="(item,index) in voice_list" :key="index" style="margin:10px 10px;width:230px;" @click.native="chooseMediaUrl(index)">
                                <div class="image_text_head">
                                    <div style="padding:10px 0px">
                                        <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                            <div style="text-align:center;">
                                                <div class="iconfont icon-paishipin" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                <div>创建于：[[item.created_at]]</div>
                                            </div>
                                            <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;height:32px;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
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
                <el-button @click="media_url = false">取 消</el-button>
                <!-- <el-button type="primary" @click="media_url = false">确 定</el-button> -->
            </span>
        </el-dialog>
        <!-- 图片选择弹出框 -->
        <el-dialog title="图片" :visible.sync="img_url" width="60%" v-loading="dialog_loading">
            <div>
                <el-tabs v-model="img_url0" @tab-click="handleClickImg">
                    <el-tab-pane label="微信" name="img_url1">
                        <el-row>
                            <el-col :span="24" align="right" style="margin:20px 0;">
                                <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.upload',['type' => 'wechat']) !!}" accept="image/*" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
                                    <el-button type="primary">上传图片</el-button>
                                </el-upload>
                            </el-col>
                        </el-row>
                        <el-row style="overflow-y: scroll;max-height:400px;">
                            <el-col :span="5" v-for="(item,index) in img_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseImgUrl(index)">
                                <div class="image_text_head">
                                    <div style="padding:10px 30px">
                                        <div class="image_text_con" style="min-width:180px;height:150px;overflow:hidden;position:relative;">
                                            <img :src="item.attachment"  style="min-width:180px;height:150px;overflow:hidden;" alt="">
                                            <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;height:32px;overflow:hidden;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
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
                    <el-row>
                        <el-col :span="24" align="right" style="margin:20px 0;">
                            <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.upload',['type' => 'local']) !!}" accept="image/*" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
                                <el-button type="primary">上传图片</el-button>
                            </el-upload>
                        </el-col>
                    </el-row>
                    <el-row style="overflow-y: scroll;max-height:400px;">
                        <el-col :span="5" v-for="(item,index) in img_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseImgUrl(index)">
                            <div class="image_text_head">
                                <div style="padding:10px 30px">
                                    <div class="image_text_con" style="min-width:180px;height:150px;overflow:hidden;position:relative;">
                                        <img :src="item.attachment"  style="min-width:180px;height:150px;overflow:hidden;" alt="">
                                        <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;height:32px;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
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
                    <el-tab-pane label="提取网络地址" name="img_url3">
                        <el-col :span="24" align="right" style="margin:20px 0;">
                           
                        </el-col>
                    </el-row>
                        <div style="color:98999a;text-align:center;font-size:26px">
                            <div style="margin:20px 0">输入图片链接</div>
                            <div>
                                <el-input style="width:60%" v-model="network_img_url" placeholder="图片链接"></el-input>
                            </div>
                            <div>
                                <el-button style="padding:10px 60px;margin:20px 0" @click="transform">转换</el-button>
                            </div>
                        </div>
                    </el-tab-pane>
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
                        <el-row>
                            <el-col :span="24" align="right" style="margin:20px 0;">
                                <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.upload',['type' => 'wechat']) !!}" accept="video/mp4" :show-file-list="false" :on-success="uploadVideoSuccess" :before-upload="beforeUploadVideo">
                                    <el-button type="primary">上传视频</el-button>
                                </el-upload>
                            </el-col>
                        </el-row>
                        <el-row style="overflow-y: scroll;max-height:400px;">
                            <el-col :span="6" v-for="(item,index) in video_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseVideoUrl(index)">
                                <div class="image_text_head">
                                    <div style="padding:10px 0px">
                                        <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                            <div style="text-align:center;">
                                            <div class="iconfont icon-shipindianbo" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                <div>创建于：[[item.createtime]]</div>
                                            </div>
                                            <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;height:32px;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
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
                        <el-row>
                            <el-col :span="24" align="right" style="margin:20px 0;">
                                <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.upload',['type' => 'local']) !!}" accept="video/mp4" :show-file-list="false" :on-success="uploadVideoSuccess" :before-upload="beforeUploadVideo">
                                    <el-button type="primary">上传视频</el-button>
                                </el-upload>
                            </el-col>
                        </el-row>
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
                        <el-row>
                            <el-col :span="24" align="right" style="margin:20px 0;">
                                <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.upload',['type' => 'wechat']) !!}" accept="audio/*" :show-file-list="false" :on-success="uploadVoiceSuccess" :before-upload="beforeUploadVoice">
                                    <el-button type="primary">上传音频</el-button>
                                </el-upload>
                            </el-col>
                        </el-row>
                        <el-row style="overflow-y: scroll;max-height:400px;">
                            <el-col :span="6" v-for="(item,index) in voice_list" :key="index" style="margin:10px 10px;width:230px;" @click.native="chooseAudioUrl(index)">
                                <div class="image_text_head">
                                    <div style="padding:10px 0px">
                                        <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                            <div style="text-align:center;">
                                                <div class="iconfont icon-paishipin" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                <div>创建于：[[item.created_at]]</div>
                                            </div>
                                            <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;height:32px;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
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
                    <el-row>
                        <el-col :span="24" align="right" style="margin:20px 0;">
                            <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.upload',['type' => 'local']) !!}" accept="audio/*" :show-file-list="false" :on-success="uploadVoiceSuccess" :before-upload="beforeUploadVoice">
                                <el-button type="primary">上传音频</el-button>
                            </el-upload>
                        </el-col>
                    </el-row>
                        <el-row style="overflow-y: scroll;max-height:400px;">
                            <el-col :span="6" v-for="(item,index) in voice_list" :key="index" style="margin:10px 10px;width:230px;" @click.native="chooseAudioUrl(index)">
                                <div class="image_text_head">
                                    <div style="padding:10px 0px">
                                        <div class="image_text_con" style="min-width:200px;height:130px;overflow:hidden;position:relative;">
                                            <div style="text-align:center;">
                                                <div class="iconfont icon-paishipin" style="display:block;font-size:80px;width:100%;height:60px;padding-top:20px;"></div>
                                                <div>创建于：[[item.created_at]]</div>
                                            </div>
                                            <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;height:32px;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
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
    </div>
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            let data = {!! $data?:'{}' !!};
            console.log(data);
            return{
                dialogTableVisible:false,
                activeNames: ['0'],
                network_img_url:"",//网络图片链接
                keyword_type:1,//关键字类型
                keyword:"",//关键字的值
                textarea:"",//文字的值
                is_text_edit:9999,//文字编辑还是添加状态
                is_exists:false,//关键字是否存在
                search_img_text:"",//图文搜索
                img_text_url:false,
                img_text_url0:"img_text_url1",
                text_url:false,
                img_url:false,
                media_url:false,//选择音乐弹出框里面的选择媒体
                img_url0:"img_url1",
                audio_url0:"audio_url1",
                video_url0:"video_url1",
                media_url0:"media_url1",
                music_url:false,
                audio_url:false,
                video_url:false,
                img_list:"",
                img_text_list:[{has_many_wechat_news:[{thumb_url:"",title:"",}]}],
                music_list:"",
                voice_list:"",
                video_list:"",
                dialog_loading:false,
                submit_loading:false,
                form:{},

                music_form:{//音乐表单
                    url:"",
                },
                data:{
                    has_many_keywords:[],
                    has_many_basic_reply:[],
                    has_many_video_reply:[],
                    has_many_news_reply:[],
                    has_many_image_reply:[],
                    has_many_voice_reply:[],
                    has_many_video_reply:[],
                    has_many_music_reply:[],
                    status:1,
                    displayorder:"",//优先级
                    ...data
                },
                // 分页
                loading:false,
                table_loading:false,
                total:0,
                per_size:0,
                current_page:0,
                rules:{
                    name: [{ required: true, message: '请输入规则名'},],
                },
                rules1:{
                    url:[{required: true, message: '请选择媒体文件'}],
                },

            }
        },
        methods: {
            // 选择回复消息弹出框
            selectMsgUrl(x) {
                console.log("选择发送消息！！！");
                var that = this;
                if(x===1) {
                    that.img_text_url = true;
                    that.dialog_loading=true,
                    that.img_text_url0 = "img_text_url1";
                    that.handleClickImgText();
                }
            
                if(x===2) {
                    that.text_url = true;
                   
                }
                if(x===3) {
                    that.img_url = true;
                    that.dialog_loading=true,
                    that.img_url0 = "img_url1";
                    that.handleClickImg();
                    
                }
                if(x===4) {
                    that.music_url = true;
                }
                if(x===5) {
                    that.audio_url = true;
                    that.dialog_loading=true,
                    that.audio_url0 = "audio_url1";
                    that.handleClickAudio();
                }
                if(x===6) {
                    that.video_url = true;
                    that.dialog_loading=true;
                    that.video_url0="video_url1";
                    that.handleClickVideo();
                    
                }
            },
            // 添加关键字
            chooseKeyword() {
                console.log(this.keyword);
                var that = this;
                let reg1 = /^.+\,+.+$/;
                let reg2 = /^.+\，+.+$/;
                if(that.keyword_type!==3){
                    if(reg1.test(that.keyword)){
                        let keyword_arr = that.keyword.split(",");
                        console.log(keyword_arr);
                        for(let i=0;i<keyword_arr.length;i++){
                            that.data.has_many_keywords.push({content:keyword_arr[i],type:that.keyword_type});
                        }
                        that.dialogTableVisible=false;
                        that.keyword="";
                        that.keyword_type=1;
                        return;
                    }
                    if(reg2.test(that.keyword)){
                        let keyword_arr = that.keyword.split("，");
                        console.log(keyword_arr);
                        for(let i=0;i<keyword_arr.length;i++){
                            that.data.has_many_keywords.push({content:keyword_arr[i],type:that.keyword_type});
                        }
                        that.dialogTableVisible=false;
                        that.keyword="";
                        that.keyword_type=1;
                        return;
                    }
                }
                that.data.has_many_keywords.push({content:that.keyword,type:that.keyword_type});
                that.dialogTableVisible=false;
                that.keyword="";
                that.keyword_type=1;
            },
            // 删除关键字
            delKeyword(index,id) {
                var that = this;
                console.log(this.data)
                that.data.has_many_keywords.splice(index,1);
            },
            // 添加文字
            chooseText(){
                console.log("hah")
                var that = this;
                if(that.textarea==""){
                    that.$message.error("文字不能为空！");
                    return;
                }
                if(that.is_text_edit!==9999){//编辑时
                    that.data.has_many_basic_reply[that.is_text_edit].content = that.textarea;
                }
                else {//添加时
                    that.data.has_many_basic_reply.push({content:that.textarea});
                }
                that.is_text_edit=9999;
                that.text_url=false;
                that.textarea="";
            },
            // 编辑文字
            editText(index){
                var that = this;
                that.text_url = true;
                that.textarea = that.data.has_many_basic_reply[index].content;
                that.is_text_edit = index;
                console.log(index);
            },
            // 删除文字
            delText(index){
                var that = this;
                that.data.has_many_basic_reply.splice(index,1);
            },
            // 删除图文
            delTextImg(index){
                var that = this;
                that.data.has_many_news_reply.splice(index,1);
            },
            // 删除图片
            delImg(index){
                var that = this;
                that.data.has_many_image_reply.splice(index,1);
            },
            // 删除语音
            delVoice(index){
                var that = this;
                that.data.has_many_voice_reply.splice(index,1);
            },
            // 删除音乐
            delMusic(index){
                var that = this;
                that.data.has_many_music_reply.splice(index,1);
            },
            // 删除视频
            delVideo(index){
                var that = this;
                that.data.has_many_video_reply.splice(index,1);
            },
            // 选择图文
            chooseImgTextUrl(index){
                var that = this;
                for(let i=0;i<that.data.has_many_news_reply.length;i++){
                    if(that.data.has_many_news_reply[i].media_id==that.img_text_list[index].id){
                        that.$message.error("此图文信息已经选择，无需重复操作！");
                        return false;
                    }
                }
                that.data.has_many_news_reply.push({
                    // id:that.img_text_list[index].id,
                    media_id:that.img_text_list[index].id,
                    title:that.img_text_list[index].has_many_wechat_news[0].title,
                    thumb:that.img_text_list[index].has_many_wechat_news[0].thumb_url,
                })
                that.img_text_url = false;
            },
            // 选择图片
            chooseImgUrl(index){
                var that = this;
                for(let i=0;i<that.data.has_many_image_reply.length;i++){
                    if(that.data.has_many_image_reply[i].mediaid==that.img_list[index].media_id){
                        that.$message.error("此图片信息已经选择，无需重复操作！");
                        return false;
                    }
                }
                if(!that.img_list[index].media_id){
                    that.submit_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.local-to-wechat') !!}",{id:that.img_list[index].id}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log("hahahahah")
                        that.data.has_many_image_reply.push({
                            // id:that.img_list[index].id,
                            mediaid:response.data.data.media_id,
                            filename:response.data.data.filename,
                            has_one_attachment:{attachment:response.data.data.attachment},
                        })
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
                that.data.has_many_image_reply.push({
                    // id:that.img_list[index].id,
                    mediaid:that.img_list[index].media_id,
                    filename:that.img_list[index].filename,
                    has_one_attachment:{attachment:that.img_list[index].attachment},
                })
                that.img_url = false;
            },
            // 转化图片网络地址
            transform() {
                var that = this;
                that.dialog_loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.fetch') !!}",{url:that.network_img_url}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log("hahahahah")
                        that.$message.success("转换成功！");
                        that.network_img_url="";
                        that.img_url = false;
                    }
                    else{
                        that.$message.error(response.data.msg);
                    }
                    that.dialog_loading = false;
                }),function(res){
                    console.log(res);
                    that.dialog_loading = false;
                    return false;
                };
            },
            // 选择视频
            chooseVideoUrl(index){
                var that = this;
                for(let i=0;i<that.data.has_many_video_reply.length;i++){
                    if(that.data.has_many_video_reply[i].mediaid==that.video_list[index].media_id){
                        that.$message.error("此视频信息已经选择，无需重复操作！");
                        return false;
                    }
                }
                if(!that.video_list[index].media_id){
                    that.submit_loading = true;
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.local-to-wechat') !!}",{id:that.video_list[index].id}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.data.has_many_video_reply.push({
                            mediaid:response.data.data.media_id,
                            filename:response.data.data.filename,
                            title:response.data.data.tag.title,
                        });
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
                that.data.has_many_video_reply.push({
                    // id:that.video_list[index].id,
                    filename:that.video_list[index].filename,
                    mediaid:that.video_list[index].media_id,
                    title:that.video_list[index].tag.title,
                })
                that.video_url = false;
            },
            // 选择语音
            chooseAudioUrl(index) {
                var that = this;
                for(let i=0;i<that.data.has_many_voice_reply.length;i++){
                    if(that.data.has_many_voice_reply[i].mediaid==that.voice_list[index].media_id){
                        that.$message.error("此语音信息已经选择，无需重复操作！");
                        return false;
                    }
                }
                if(!that.voice_list[index].media_id){
                    that.submit_loading = true;
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.local-to-wechat') !!}",{id:that.voice_list[index].id}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.data.has_many_voice_reply.push({
                            mediaid:response.data.data.media_id,
                            filename:response.data.data.filename,
                            title:response.data.data.tag.title,
                        });
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
                that.data.has_many_voice_reply.push({
                    // id:that.voice_list[index].id,
                    filename:that.voice_list[index].filename,
                    mediaid:that.voice_list[index].media_id,
                    title:that.voice_list[index].tag.title,
                })
                that.audio_url = false;
            },
            // 选择音乐
            chooseMusicUrl(){
                var that = this;
                that.$refs['music_form'].validate((valid) => {
                    if (valid) {
                        console.log("sdsdsd")
                        that.data.has_many_music_reply.push(that.music_form);
                        that.music_url = false;
                        console.log(that.data.has_many_music_reply);
                        that.music_form={};
                    }
                });
            },
            // 选择音乐url
            chooseMediaUrl(index) {
                var that = this;
                if(!that.voice_list[index].media_id){
                    that.submit_loading = true;
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.local-to-wechat') !!}",{id:that.voice_list[index].id}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.music_form.url = response.data.data.attachment;
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
                that.media_url = false;
                return false;
                }
                that.music_form.url = that.voice_list[index].attachment;
                that.media_url = false;
                console.log(that.music_form);
            },
            // 上传图片之前
            beforeUpload(){
                this.dialog_loading=true;
            },
            // 上传图片成功之后
            uploadSuccess(response,file,fileList){
                if(response.result==1){
                    this.$message.success("上传成功！")
                    this.handleClickImg();
                }
                else{
                    this.$message.error(response.msg);
                }
                this.dialog_loading=false;
                this.$message.success("上传成功！")
            },
            
            // 上传语音之前
            beforeUploadVoice(){
                this.dialog_loading=true;
            },
            // 上传语音成功之后
            uploadVoiceSuccess(response,file,fileList){
                console.log(response);
                if(response.result==1){
                    this.$message.success("上传成功！")
                    this.handleClickAudio();
                }
                else{
                    this.$message.error(response.msg);
                }
                this.dialog_loading=false;
            },
            // 上传音乐成功之后
            uploadVoiceSuccess1(response,file,fileList){
                console.log(response);
                if(response.result==1){
                    this.$message.success("上传成功！")
                    this.handleClickMedia();
                }
                else{
                    this.$message.error(response.msg);
                }
                this.dialog_loading=false;
            },
            // 上传视频之前
            beforeUploadVideo(){
                this.dialog_loading=true;
            },
            // 上传视频成功之后
            uploadVideoSuccess(response,file,fileList){
                console.log(response);
                if(response.result==1){
                    this.$message.success("上传成功！")
                    this.handleClickVideo();
                }
                else{
                    this.$message.error(response.msg);
                }
                this.dialog_loading=false;
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
            // 保存
            submit(formName){
                console.log(this.form);
                console.log(this.data);
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.submit_loading = true;
                        delete(this.form['thumb_url']);
                        this.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.save') !!}",{'form_data':this.data}).then(response => {
                            if (response.data.result) {
                                this.$message({type: 'success',message: '操作成功!'});
                                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.index') !!}';
                            } else {
                                this.$message({message: response.data.msg,type: 'error'});
                                this.submit_loading = false;
                            }
                        },response => {
                            this.submit_loading = false;
                        });
                    }
                    else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            handleClick(tab, event) {
                console.log(event);
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
                this.search_img_text = '';
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
            // 音乐url弹出框里的tabs
            handleClickMedia() {
                var that = this;
                that.media_url = true;
                if(that.media_url0 == "media_url1"){
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
                else if(that.media_url0 == "media_url2"){
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
            close() {
                this.dialogTableVisible=false;
            }
        },
    })

</script>
@endsection
