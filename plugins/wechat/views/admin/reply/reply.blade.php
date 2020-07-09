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
    /* 默认回复 */
    .default_hover{line-height:100px;border:1px solid #e7e7eb;text-align:center;width:25%;}
    .default_hover:hover{background:#f4f6f9;color:#428bca;cursor:pointer;}

    [v-cloak]{
        display:none;
    }
</style>
<div class="rightlist">
    <div id="app" v-loading="all_loading" v-cloak>
        <div class="rightlist-head">
            <div class="rightlist-head-con">自动回复</div>
        </div>
        <el-tabs v-model="activeName" type="card" @tab-click="handleClick" v-loading="loading">
            <el-tab-pane label="关键字自动回复" name="keyword">
                <el-form :model="search_form" ref="search_form">
                    <el-form-item>
                        <el-col :span="16">
                            <el-select v-model="search_form.type" clearable style="width:150px">
                                <el-option v-for="(item,index) in type_list" :value="item.id" :key="index" :label="item.name"></el-option>
                                <!-- <el-option value="2" label="关键字"></el-option> -->

                            </el-select>
                            <el-input type="text" style="width:40%" v-model="search_form.search"></el-input>
                            <el-button @click="searchForm"><i class="el-icon-search"></i></el-button>
                        </el-col>
                        <el-col :span="8" align="right">
                            <a href="{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit') }}">
                                <el-button type="primary"><i class="el-icon-plus">添加关键字回复</i></el-button>
                            </a>
                           
                            <!-- <el-button type="danger"><i class="el-icon-plus">添加应用关键字</i></el-button> -->
                        </el-col>
                    </el-form-item>
                </el-form>
                <el-tabs v-model="activeName1" type="buttom" @tab-click="handleClickChild">
                    <el-tab-pane label="全部" name="all">
                       
                        <div v-for="(item,index) in data" :key="index" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                            <div style="width:100%;border-bottom:1px solid #e7e7eb;background:#f4f6f9;">
                                <el-row style="width:90%;margin:0 0 20px 5%;line-height:50px;">
                                    <el-col :span="12">
                                        <!-- <el-checkbox v-model.number="item.is_checked" :true-label="1" :false-label="0" ></el-checkbox> -->
                                        <span v-if="item.name"> 规则名：[[item.name]]</span>
                                        <span v-else>&nbsp;</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>是否开启</div>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>操作</div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div style="width:90%;margin:0 0 20px 5%;padding:50px 0;">
                                <el-row>
                                    <el-col :span="12">
                                        <div style="padding-bottom:15px;">
                                            <div style="width:90px;display:inline-block;">[关键字]</div>
                                            <span v-for="(list,index1) in item.has_many_keywords" style="background:#e7e8eb;padding:5px;margin:0 5px;">
                                                [[list.content]]
                                            </span>
                                        </div>
                                        <div style="width:90px;display:inline-block;">回复内容</div>
                                        <span v-if="item.replySum==0">应用回复</span>
                                        <span v-if="item.replySum>0">共[[item.replySum]]条</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <el-switch v-model="item.status" :active-value="1" :inactive-value="0" @change="changeStatus(index,item.id,item)"></el-switch>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <a :href="'{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',array('id' => '')) }}'+[[item.id]]">
                                            <el-button>编辑</el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(index,item.id)">删除</el-button>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <!-- 分页 -->
                        <el-row>
                            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                <el-pagination layout="prev, pager, next" @current-change="currentChangeAll" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="回复图文" name="img_word">
                        
                        <div v-for="(item,index) in data" :key="index" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                            <div style="width:100%;border-bottom:1px solid #e7e7eb;background:#f4f6f9;">
                                <el-row style="width:90%;margin:0 0 20px 5%;line-height:50px;">
                                    <el-col :span="12">
                                        <!-- <el-checkbox v-model.number="item.is_checked" :true-label="1" :false-label="0" ></el-checkbox> -->
                                        <span v-if="item.name"> 规则名：[[item.name]]</span>
                                        <span v-else>&nbsp;</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>是否开启</div>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>操作</div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div style="width:90%;margin:0 0 20px 5%;padding:50px 0;">
                                <el-row>
                                    <el-col :span="12">
                                        <div style="padding-bottom:15px;">
                                            <div style="width:90px;display:inline-block;">[关键字]</div>
                                            <span v-for="(list,index1) in item.has_many_keywords" style="background:#e7e8eb;padding:5px;margin:0 5px;">
                                                [[list.content]]
                                            </span>
                                        </div>
                                        <div style="width:90px;display:inline-block;">回复内容</div>
                                        <span v-if="item.replySum==0">应用回复</span>
                                        <span v-if="item.replySum>0">共[[item.replySum]]条</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <el-switch v-model="item.status" :active-value="1" :inactive-value="0" @change="changeStatus(index,item.id,item)"></el-switch>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <a :href="'{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',array('id' => '')) }}'+[[item.id]]">
                                            <el-button>编辑</el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(index,item.id)">删除</el-button>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <!-- 分页 -->
                        <el-row>
                            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                <el-pagination layout="prev, pager, next" @current-change="currentChangeNews" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <!-- <el-tab-pane label="回复模块" name="module">
                        
                    <div v-for="(item,index) in data" :key="index" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                            <div style="width:100%;border-bottom:1px solid #e7e7eb;background:#f4f6f9;">
                                <el-row style="width:90%;margin:0 0 20px 5%;line-height:50px;">
                                    <el-col :span="12">
                                        <span v-if="item.name"> 规则名：[[item.name]]</span>
                                        <span v-else>&nbsp;</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>是否开启</div>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>操作</div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div style="width:90%;margin:0 0 20px 5%;padding:50px 0;">
                                <el-row>
                                    <el-col :span="12">
                                        <div style="padding-bottom:15px;">
                                            <div style="width:90px;display:inline-block;">[关键字]</div>
                                            <span v-for="(list,index1) in item.has_many_keywords" style="background:#e7e8eb;padding:5px;margin:0 5px;">
                                                [[list.content]]
                                            </span>
                                        </div>
                                        <div style="width:90px;display:inline-block;">回复内容</div>
                                        <span v-if="item.replySum==0">应用回复</span>
                                        <span v-if="item.replySum>0">共[[item.replySum]]条</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <el-switch v-model="item.status" :active-value="1" :inactive-value="0" @change="changeStatus(index,item.id,item)"></el-switch>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <a :href="'{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',array('id' => '')) }}'+[[item.id]]">
                                            <el-button>编辑</el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(index,item.id)">删除</el-button>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                    </el-tab-pane> -->
                    <el-tab-pane label="回复语音" name="voice">
                        
                        <div v-for="(item,index) in data" :key="index" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                            <div style="width:100%;border-bottom:1px solid #e7e7eb;background:#f4f6f9;">
                                <el-row style="width:90%;margin:0 0 20px 5%;line-height:50px;">
                                    <el-col :span="12">
                                        <!-- <el-checkbox v-model.number="item.is_checked" :true-label="1" :false-label="0" ></el-checkbox> -->
                                        <span v-if="item.name"> 规则名：[[item.name]]</span>
                                        <span v-else>&nbsp;</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>是否开启</div>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>操作</div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div style="width:90%;margin:0 0 20px 5%;padding:50px 0;">
                                <el-row>
                                    <el-col :span="12">
                                        <div style="padding-bottom:15px;">
                                            <div style="width:90px;display:inline-block;">[关键字]</div>
                                            <span v-for="(list,index1) in item.has_many_keywords" style="background:#e7e8eb;padding:5px;margin:0 5px;">
                                                [[list.content]]
                                            </span>
                                        </div>
                                        <div style="width:90px;display:inline-block;">回复内容</div>
                                        <span v-if="item.replySum==0">应用回复</span>
                                        <span v-if="item.replySum>0">共[[item.replySum]]条</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <el-switch v-model="item.status" :active-value="1" :inactive-value="0" @change="changeStatus(index,item.id,item)"></el-switch>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <a :href="'{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',array('id' => '')) }}'+[[item.id]]">
                                            <el-button>编辑</el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(index,item.id)">删除</el-button>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <!-- 分页 -->
                        <el-row>
                            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                <el-pagination layout="prev, pager, next" @current-change="currentChangeVoice" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="回复文字" name="word">
                        
                        <div v-for="(item,index) in data" :key="index" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                            <div style="width:100%;border-bottom:1px solid #e7e7eb;background:#f4f6f9;">
                                <el-row style="width:90%;margin:0 0 20px 5%;line-height:50px;">
                                    <el-col :span="12">
                                        <!-- <el-checkbox v-model.number="item.is_checked" :true-label="1" :false-label="0" ></el-checkbox> -->
                                        <span v-if="item.name"> 规则名：[[item.name]]</span>
                                        <span v-else>&nbsp;</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>是否开启</div>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>操作</div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div style="width:90%;margin:0 0 20px 5%;padding:50px 0;">
                                <el-row>
                                    <el-col :span="12">
                                        <div style="padding-bottom:15px;">
                                            <div style="width:90px;display:inline-block;">[关键字]</div>
                                            <span v-for="(list,index1) in item.has_many_keywords" style="background:#e7e8eb;padding:5px;margin:0 5px;">
                                                [[list.content]]
                                            </span>
                                        </div>
                                        <div style="width:90px;display:inline-block;">回复内容</div>
                                        <span v-if="item.replySum==0">应用回复</span>
                                        <span v-if="item.replySum>0">共[[item.replySum]]条</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <el-switch v-model="item.status" :active-value="1" :inactive-value="0" @change="changeStatus(index,item.id,item)"></el-switch>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <a :href="'{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',array('id' => '')) }}'+[[item.id]]">
                                            <el-button>编辑</el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(index,item.id)">删除</el-button>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <!-- 分页 -->
                        <el-row>
                            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                <el-pagination layout="prev, pager, next" @current-change="currentChangeWord" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="回复音乐" name="music">
                    
                        <div v-for="(item,index) in data" :key="index" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                            <div style="width:100%;border-bottom:1px solid #e7e7eb;background:#f4f6f9;">
                                <el-row style="width:90%;margin:0 0 20px 5%;line-height:50px;">
                                    <el-col :span="12">
                                        <!-- <el-checkbox v-model.number="item.is_checked" :true-label="1" :false-label="0" ></el-checkbox> -->
                                        <span v-if="item.name"> 规则名：[[item.name]]</span>
                                        <span v-else>&nbsp;</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>是否开启</div>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>操作</div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div style="width:90%;margin:0 0 20px 5%;padding:50px 0;">
                                <el-row>
                                    <el-col :span="12">
                                        <div style="padding-bottom:15px;">
                                            <div style="width:90px;display:inline-block;">[关键字]</div>
                                            <span v-for="(list,index1) in item.has_many_keywords" style="background:#e7e8eb;padding:5px;margin:0 5px;">
                                                [[list.content]]
                                            </span>
                                        </div>
                                        <div style="width:90px;display:inline-block;">回复内容</div>
                                        <span v-if="item.replySum==0">应用回复</span>
                                        <span v-if="item.replySum>0">共[[item.replySum]]条</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <el-switch v-model="item.status" :active-value="1" :inactive-value="0" @change="changeStatus(index,item.id,item)"></el-switch>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <a :href="'{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',array('id' => '')) }}'+[[item.id]]">
                                            <el-button>编辑</el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(index,item.id)">删除</el-button>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <!-- 分页 -->
                        <el-row>
                            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                <el-pagination layout="prev, pager, next" @current-change="currentChangeMusic" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="回复图片" name="img">
                        
                        <div v-for="(item,index) in data" :key="index" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                            <div style="width:100%;border-bottom:1px solid #e7e7eb;background:#f4f6f9;">
                                <el-row style="width:90%;margin:0 0 20px 5%;line-height:50px;">
                                    <el-col :span="12">
                                        <!-- <el-checkbox v-model.number="item.is_checked" :true-label="1" :false-label="0" ></el-checkbox> -->
                                        <span v-if="item.name"> 规则名：[[item.name]]</span>
                                        <span v-else>&nbsp;</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>是否开启</div>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>操作</div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div style="width:90%;margin:0 0 20px 5%;padding:50px 0;">
                                <el-row>
                                    <el-col :span="12">
                                        <div style="padding-bottom:15px;">
                                            <div style="width:90px;display:inline-block;">[关键字]</div>
                                            <span v-for="(list,index1) in item.has_many_keywords" style="background:#e7e8eb;padding:5px;margin:0 5px;">
                                                [[list.content]]
                                            </span>
                                        </div>
                                        <div style="width:90px;display:inline-block;">回复内容</div>
                                        <span v-if="item.replySum==0">应用回复</span>
                                        <span v-if="item.replySum>0">共[[item.replySum]]条</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <el-switch v-model="item.status" :active-value="1" :inactive-value="0" @change="changeStatus(index,item.id,item)"></el-switch>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <a :href="'{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',array('id' => '')) }}'+[[item.id]]">
                                            <el-button>编辑</el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(index,item.id)">删除</el-button>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <!-- 分页 -->
                        <el-row>
                            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                <el-pagination layout="prev, pager, next" @current-change="currentChangeImage" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="回复视频" name="video">
                        
                        <div v-for="(item,index) in data" :key="index" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                            <div style="width:100%;border-bottom:1px solid #e7e7eb;background:#f4f6f9;">
                                <el-row style="width:90%;margin:0 0 20px 5%;line-height:50px;">
                                    <el-col :span="12">
                                        <!-- <el-checkbox v-model.number="item.is_checked" :true-label="1" :false-label="0" ></el-checkbox> -->
                                        <span v-if="item.name"> 规则名：[[item.name]]</span>
                                        <span v-else>&nbsp;</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>是否开启</div>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <div>操作</div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div style="width:90%;margin:0 0 20px 5%;padding:50px 0;">
                                <el-row>
                                    <el-col :span="12">
                                        <div style="padding-bottom:15px;">
                                            <div style="width:90px;display:inline-block;">[关键字]</div>
                                            <span v-for="(list,index1) in item.has_many_keywords" style="background:#e7e8eb;padding:5px;margin:0 5px;">
                                                [[list.content]]
                                            </span>
                                        </div>
                                        <div style="width:90px;display:inline-block;">回复内容</div>
                                        <span v-if="item.replySum==0">应用回复</span>
                                        <span v-if="item.replySum>0">共[[item.replySum]]条</span>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <el-switch v-model="item.status" :active-value="1" :inactive-value="0" @change="changeStatus(index,item.id,item)"></el-switch>
                                    </el-col>
                                    <el-col :span="6" align="center">
                                        <a :href="'{{ yzWebUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.edit',array('id' => '')) }}'+[[item.id]]">
                                            <el-button>编辑</el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(index,item.id)">删除</el-button>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <!-- 分页 -->
                        <el-row>
                            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                <el-pagination layout="prev, pager, next" @current-change="currentChangeVideo" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </el-tab-pane>


                </el-tabs>
            </el-tab-pane>
            <!-- <el-tab-pane label="非文字自动回复" name="not_word">
                <el-table  :data="list" style="width: 100%">
                    <el-table-column prop="type" label="类型"align="center"></el-table-column>
                    <el-table-column prop="keyword" label="关键字/模板"align="center"></el-table-column>
                    <el-table-column label="状态"align="center">
                        <template slot-scope="scope">
                            <el-tooltip :content="scope.row.is_open?'已开启':'已关闭'" placement="top">
                                <el-switch v-model="scope.row.is_open" :active-value="1" :inactive-value="0" @change="statusChange(scope.$index,scope.row,scope.row.id)"></el-switch>
                            </el-tooltip>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作"align="center">
                        <template slot-scope="scope">
                            <a href="#">
                                <el-button type="primary">编辑</el-button>
                            </a>
                        </template>
                    </el-table-column>
                </el-table>
            </el-tab-pane> -->
            <el-tab-pane label="首次访问自动回复" name="first">
                
            </el-tab-pane>
            <el-tab-pane label="默认回复" name="default_text">
                
            </el-tab-pane>
        </el-tabs>
    </div>
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            // let data = {!! $data?:'{}' !!};
            // console.log(data);
            return{
                activeName:"keyword",
                activeName1:"all",
                dialogTableVisible:false,
                loading:false,
                all_loading:false,
                search_form:{},
                type:"",
                data:[],
                 // 分页
                table_loading:false,
                total:0,
                per_size:0,
                current_page:0,
                type_list:[
                    {id:1,name:"规则名"},
                    {id:2,name:"关键字"},
                ],
                list:[
                    {id:1,type:"图片消息",keyword:"关键字模板",is_open:1,},
                    {id:1,type:"语音消息",keyword:"关键字模板",is_open:0,},
                    {id:1,type:"视频消息",keyword:"关键字模板",is_open:1,},
                    {id:1,type:"位置消息",keyword:"关键字模板",is_open:1,},

                ],
                rules:{},
            }
        },
        created () {
          this.getALLData();  
        },
        methods: {
            handleClickChild(tab, event) {
                console.log("haha")
                var that = this;
                // var type = "";
                that.loading = true;
                if(tab.name == "all"){
                    that.type="";
                    // window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.index') !!}';
                }
                else if(tab.name == "img_word"){
                    that.type="news";
                }
                else if(tab.name == "voice"){
                    that.type="voice";
                }
                else if(tab.name == "word"){
                    that.type="basic";
                }
                else if(tab.name == "img"){
                    that.type="images";
                }
                else if(tab.name == "video"){
                    that.type="video";
                }
                else if(tab.name == "music"){
                    that.type="music";
                }
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}",{type:that.type,search_type:that.search_form.type,search:that.search_form.search}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log("hahahahah")
                        that.data = response.data.data.data;
                        that.loading = false;
                    }
                    that.loading = false;
                }),function(res){
                    console.log(res);
                    that.loading = false;
                };
            },
            getALLData(){
                var that = this;
                that.loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}",{search_type:that.search_form.type,search:that.search_form.search}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log("hahahahah")
                        that.data = response.data.data.data;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.loading = false;
                    }
                    that.loading = false;
                }),function(res){
                    console.log(res);
                    that.loading = false;
                };
            },
            // 搜索
            searchForm() {
                console.log(this.search_form);
                var that = this;
                that.loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}",{type:that.type,search_type:that.search_form.type,search:that.search_form.search}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log("hahahahah")
                        that.data = response.data.data.data;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;
                        that.current_page = response.data.data.current_page;
                        that.loading = false;
                    }
                    that.loading = false;
                }),function(res){
                    console.log(res);
                    that.loading = false;
                };
            },
            // 开启关闭
            changeStatus(index,id,item){
                console.log(index,id);
                this.loading=true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.status') !!}',{id:id,status:item.status}).then(function (response) {
                    if (response.data.result){
                        if(item.status)
                            this.$message({type: 'success',message: '开启成功!'});
                        else
                            this.$message({type: 'success',message: '关闭成功!'});
                    }
                    else {
                        this.$message({type: 'error',message: response.data.msg});
                        if (this.data[index].status==1){
                            this.data[index].status = 0;
                        }
                        else{
                            this.data[index].status = 1;
                        }
                    }
                    this.loading=false;
                },function (response) {
                    this.$message({type: 'error',message: response.data.msg});
                    if (this.data[index].status==1){
                        this.data[index].status = 0;
                    }
                    else{
                        this.data[index].status = 1;
                    }
                    this.loading=false;
                }
                );

            },
            delRow(index,id) {
                console.log(this.data)
                this.$confirm('确定删除吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                    this.loading=true;
                    this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.delete') !!}',{id:id}).then(function (response) {
                    if (response.data.result) {
                        this.data.splice(index,1);
                        this.$message({type: 'success',message: '删除成功!'});
                    }
                    else{
                        this.$message({type: 'error',message: '删除失败!'});
                    }
                    this.loading=false;
                },function (response) {
                    this.$message({type: 'error',message: response.msg});
                    this.loading=false;
                }
                );
                }).catch(() => {
                    this.$message({type: 'info',message: '已取消删除'});
                    });

            },
            handleClick(tab, event) {
                console.log(event);
                this.all_loading=true;
                if(tab.name == "keyword"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.index') !!}';
                }
                if(tab.name == "default_text"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.default-reply.index') !!}';
                }
                if(tab.name == "first"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.welcome-auto-reply.index') !!}';
                }
            },
            chooseKeyword() {
                console.log("hahah");
                this.dialogTableVisible=true;
            },
            close() {
                this.dialogTableVisible=false;
            },
            // 全部回复分页
            currentChangeAll(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}',{type:'',page:val}).then(function (response){
                    console.log(response);
                    this.data = response.data.data.data;
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
            // 图文回复分页
            currentChangeNews(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}',{type:'news',page:val}).then(function (response){
                    console.log(response);
                    this.data = response.data.data.data;
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
            // 语音回复分页
            currentChangeVoice(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}',{type:'voice',page:val}).then(function (response){
                    console.log(response);
                    this.data = response.data.data.data;
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
            // 视频回复分页
            currentChangeVideo(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}',{type:'video',page:val}).then(function (response){
                    console.log(response);
                    this.data = response.data.data.data;
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
            //  视频回复分页
            currentChangeImage(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}',{type:'images',page:val}).then(function (response){
                    console.log(response);
                    this.data = response.data.data.data;
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
            //  音乐回复分页
            currentChangeMusic(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}',{type:'music',page:val}).then(function (response){
                    console.log(response);
                    this.data = response.data.data.data;
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
            
            //  音乐回复分页
            currentChangeWord(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.search') !!}',{type:'basic',page:val}).then(function (response){
                    console.log(response);
                    this.data = response.data.data.data;
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
        },
    })

</script>
@endsection
