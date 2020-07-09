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
     /* 选择图文 */
     .image_text_head{border:1px solid #dadada;}
    .image_text_head:hover{border:1px #428bca solid;cursor: pointer;color:#428bca; background:#f4f6f9;}
</style>
<div class="rightlist">
    <div id="app" v-loading="all_loading">
        <div class="rightlist-head">
            <div class="rightlist-head-con">自动回复</div>
        </div>
        <el-tabs v-model="activeName" type="card" @tab-click="handleClick" v-loading="loading">
            <el-tab-pane label="关键字自动回复" name="keyword">
                关键字自动回复
            </el-tab-pane>
            <el-tab-pane label="首次访问自动回复" name="first">
                首次访问自动回复
            </el-tab-pane>
            <el-tab-pane label="默认回复" name="default_text">
                <el-card shadow="always" style="background:#f5f5f9;">
                    <i class="el-icon-info" style="color:#409EFF;margin:0 10px;"></i>当系统不知道该如何回复粉丝的消息时，默认发送的内容。
                </el-card>
                <div>
                    <div class="" style="width:90%;margin-left:5%;border:1px solid #e7e7eb;margin-top:30px;">
                        <div style="line-height:50px;padding-left:50px;background:#f4f6f9;">触发后回复内容</div>
                        <div v-if="!data.id" style="width:90%;margin:20px 0 20px 5%;border:1px dashed #e7e7eb;padding:20px;">
                            <div class="default_hover" @click="chooseKeyword()">
                                <i class="el-icon-edit" style="color:#409EFF;"></i>触发关键词
                            </div>
                        </div>
                        <div v-if="data.id" style="width:90%;margin:20px 0 20px 5%;border:1px dashed #e7e7eb;height:130px">
                            <el-col :span="16" style="padding:50px 0 0 15px">
                                【关键字】<span style="background:#e7e8eb;padding:5px;margin:0 5px;">[[data.content]]</span>
                            </el-col>
                            <el-col :span="8" align="right" style="padding:50px 30px 0 15px">
                                <!-- <el-button>编辑</el-button> -->
                                <el-button type="danger" @click="delKeyword">删除</el-button>
                            </el-col>
                        </div>

                    </div>
                </div>
                <el-col :span="24" align="center" style="padding:30px 0;">
                    <el-button type="primary" @click="submit">保存</el-button>
                </el-col>
                <el-dialog title="关键字" :visible.sync="keyword_url" v-loading="dialog_loading" width="60%">
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
            </el-tab-pane>
        </el-tabs>
    </div>
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            let data = {!! $data?:'{}' !!};
            console.log(data);
            return{
                activeName:"default_text",
                activeName1:"all",
                keyword_url:false,
                keyword_list:[],
                search_keyword:'',
                dialogTableVisible:false,
                loading:false,
                all_loading:false,
                dialog_loading:false,
                search_form:{},
                type:"",
                data:data,
                rules:{},
            }
        },
        methods: {
            // 发送消息-选择关键字
            chooseKeywordUrl(index) {
                var that = this;
                that.data.id = that.keyword_list[index].id;
                that.data.content = that.keyword_list[index].content;
                that.keyword_url = false;

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
                var that = this;
                that.keyword_url=true;
                that.dialog_loading=true,
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.keywords-auto-reply.get-keywords') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.keyword_list = response.data.data;
                        that.dialog_loading = false;
                    }
                    that.dialog_loading = false;
                }),function(res){
                    console.log(res);
                    that.dialog_loading = false;
                };
            },
            close() {
                this.keyword_url=false;
            },
            delKeyword() {
                var that = this;
                console.log(this.data);
                if(that.data.is_set==0){
                    that.data={is_set:0};
                    return false;
                }
                that.all_loading = true;
                that.$http.get("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.default-reply.delete') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.keyword_list = response.data.data;
                        that.$message.success("删除成功！");
                        that.data = {is_set:1};
                        that.all_loading = false;
                    }
                    else{
                        that.$message.error(response.data.msg);
                    }
                    that.all_loading = false;
                }),function(res){
                    console.log(res);
                    that.all_loading = false;
                };
                
            },
            submit(){
                var that = this;
                that.all_loading = true;
                console.log(this.data);
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.default-reply.add') !!}",{keywords_id:that.data.id}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.keyword_list = response.data.data;
                        that.$message.success("保存成功！");
                        window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.reply.controller.default-reply.index') !!}';
                    }
                    else{
                        that.$message.error(response.data.msg);
                    }
                    that.all_loading = false;
                }),function(res){
                    console.log(res);
                    that.all_loading = false;
                };
            },
        },
    })

</script>
@endsection
