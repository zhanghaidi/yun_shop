@extends('layouts.base')
@section('title', "公众号素材")
@section('content')
<style>
    .rightlist #app .rightlist-head{line-height:50px;padding:15px 0;}
    .rightlist #app{margin-left:30px;}
    .el-form-item__label{padding-right:30px;}
    .tip{font-size:12px;color:#999;}
    /* .rightlist-head-con{padding-right:20px;font-size:16px;color:#888;} */
    .rightlist-head-con{float:left;padding-right:20px;font-size:16px;color:#888;}
    .el-tag{font-weight:700;font-size:15px;margin-bottom:30px;}
    .el-icon-edit{font-size:16px;padding:0 15px;color:#409EFF;cursor: pointer;}
    /* 滑块选择小白点 */
    .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
    .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
    /* 微信公众号布局 */
    .image_text_head{width:100%;border:1px solid #ccc;box-shadow: 2px 2px 5px #888888;}
    .image_text_head_time{width:90%;line-height:20px;margin-left:5%;padding:10px 0;border-bottom:1px solid #ccc;}
    .image_text_con{width:330px;height:200px;overflow:hidden;position:relative;}
    .image_text_con img{min-width:330px;height:200px;}
    .image_text_con_title{position:absolute;bottom:0;width:100%;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;}
    .image_text_list{height:100px;border-top:1px solid #ccc;padding:0 30px;overflow:hidden;}
    .image_text_list_title{width:80%;display:inline-block;}
    .image_text_list_img{width:18%;display:inline-block;padding-top:10px;}
    
    .photo_text_con{width:300px;height:200px;overflow:hidden;}
    .photo_text_con img{width:300px;height:220px;}
    input[type=file] {display: none;}

    .voice_con{width:300px;height:50px;overflow:hidden;position:relative;}
    .voice_con img{width:50px;height:50px;}
    .voice_con_time{display:inline-block;line-height:50px;margin-left:20px}

    [v-cloak]{
        display:none;
    }
</style>

<div class="rightlist">
    <div id="app" v-loading="loading" v-cloak>
    <link rel="stylesheet" href="//at.alicdn.com/t/font_913727_gt395lrelsk.css">
   
        <div class="rightlist-head">
            <div class="rightlist-head-con">公众号素材</div>
            <div class="text-align:right">
                <el-button type="primary" @click="syncWechat()">
                    <span v-if="activeName=='image_text'">同步公众号图文素材</span>
                    <span v-if="activeName=='photo'">同步公众号图片素材</span>
                    <span v-if="activeName=='voice'">同步公众号语音素材</span>
                    <span v-if="activeName=='video'">同步公众号视频素材</span>
                </el-button>
            </div>

        </div>
        <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
            <el-tab-pane label="图文" name="image_text">
                <div class="image_text">
                    <el-row>
                        <el-col :span="23" align="right">
                            <a href="{{ yzWebFullUrl('plugin.wechat.admin.material.controller.news.edit') }}">
                                <el-button type="primary">新增图文</el-button>
                            </a> 
                        </el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="6" style="margin:10px 30px;width:400px" v-for="(item,index) in datas" :key="index">
                            <div class="image_text_head">
                                <div style="width:100%;">
                                    <div class="image_text_head_time">
                                        <div>
                                            <div style="width:80%;display:inline-block;">[[item.created_at]]</div>
                                            <i @click="del('news',item.id)" class="iconfont icon-shanchu" style="float:right;display:inline-block;width:30px;height:30px;cursor:pointer" title="删除"></i>
                                            <a :href="'{{ yzWebUrl('plugin.wechat.admin.material.controller.news.edit', array('id' => '')) }}'+[[item.id]]" style="color:#000;">
                                                <i class="iconfont icon-bianjiqianbixieshuru2" style="float:right;display:inline-block;width:20px;height:30px;" title="编辑"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin:10px 30px">
                                   
                                    <div class="image_text_con">
                                        <img :src="item.has_many_wechat_news[0].thumb_url" alt="">
                                        <div class="image_text_con_title">[[item.has_many_wechat_news[0].title]]</div>
                                    </div>
                                </div>
                                <div class="image_text_list" v-for="(list,index) in item.has_many_wechat_news" :key="index" v-if="index!=0">
                                    <div class="image_text_list_title">
                                        [[list.title]]
                                    </div>
                                    <div class="image_text_list_img">
                                        <img style="width:80px;height:80px;" :src="list.thumb_url" alt="">
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
                </div>
            </el-tab-pane>
            <el-tab-pane label="图片" name="photo">
                <el-row>
                    <el-col :span="23" align="right">
                        <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.upload',['type' => 'wechat']) !!}" accept="image/*" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
                            <el-button type="primary">上传图片</el-button>
                        </el-upload> 
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="5" style="margin:10px 30px;width:330px;" v-for="(item,index) in photolist" :key="index">
                        <div class="image_text_head">
                            <div style="width:100%;">
                                <div class="image_text_head_time">
                                    <div>
                                        <div style="width:80%;display:inline-block;overflow:hidden;line-height:30px;height:30px">[[item.filename]]</div>
                                        <i @click="del('image',item.id)" class="iconfont icon-shanchu" style="float:right;display:inline-block;width:30px;height:30px;cursor:pointer" title="删除"></i>
                                    </div>
                                </div>
                            </div>
                            <div style="margin:10px">
                                <div class="photo_text_con">
                                    <img :src="item.attachment"  alt="">
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
            
            <el-tab-pane label="语音" name="voice">
                <el-row>
                    <el-col :span="23" align="right">
                        <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.upload',['type' => 'wechat']) !!}" accept="audio/*" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
                            <el-button type="primary">上传音频</el-button>
                        </el-upload> 
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="5" style="margin:10px 30px;width:330px;" v-for="(item,index) in voicelist" :key="index">
                        <div class="image_text_head">
                            <div style="width:100%;">
                                <div class="image_text_head_time">
                                    <div>
                                        <div style="width:80%;display:inline-block;overflow:hidden;line-height:30px;height:30px">[[item.filename]]</div>
                                        <i @click="del('voice',item.id)" class="iconfont icon-shanchu" style="float:right;display:inline-block;width:30px;height:30px;cursor:pointer" title="删除"></i>
                                    </div>
                                </div>
                            </div>
                            <audio :src="item.attachment"  controls="controls" style="margin:10px 5px;"></audio>
                            <!-- <div style="margin:20px 30px">
                                <div class="voice_con">
                                    <div class="iconfont icon-paishipin" style="display:block;font-size:40px;width:100%;height:60px;padding-top:20px;"></div>
                                    <div class="voice_con_time">[[item.time_length]]</div>
                                </div>
                            </div> -->
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
            <el-tab-pane label="视频" name="video">
                <el-row>
                    <el-col :span="23" align="right">
                        <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.upload',['type' => 'wechat']) !!}" accept="video/mp4" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
                            <el-button type="primary">上传视频</el-button>
                        </el-upload> 
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="5" style="margin:10px 30px;width:330px;" v-for="(item,index) in videolist" :key="index">
                        <div class="image_text_head">
                            <div style="width:100%;">
                                <div class="image_text_head_time">
                                    <div>
                                        <div style="width:80%;display:inline-block;overflow:hidden;line-height:30px;height:30px">[[item.filename]]</div>
                                        <i @click="del('video',item.id)" class="iconfont icon-shanchu" style="float:right;display:inline-block;width:30px;height:30px;cursor:pointer" title="删除"></i>
                                    </div>
                                </div>
                            </div>
                            <div style="margin:10px">
                                <div class="photo_text_con">
                                    <video :src="item.attachment"  controls="controls"></video>
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
        </el-tabs>
    </div>
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            // let data = {!! $data?:'{}' !!};
            // console.log(data);
            // for(let i=0;i<data.data.length;i++){
            //     data.data[i].createtime = new Date(parseInt(data.data[i].createtime) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
            // }
            return{
                activeName:"image_text",
                datas:[],
                loading:false,
                photolist:"",
                voicelist:"",
                videolist:"",
                 // 分页
                table_loading:false,
                total:0,
                per_size:0,
                current_page:0,
                rules:{},
            }
        },
        created () {
            this.getNewsdata();
        },
        methods: {
            handleClick() {
                var that = this;
                that.loading = true;
                // console.log(tab.name);
                if(that.activeName == "image_text") {
                    this.getNewsdata();
                    // window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.index') !!}';
                }
                if(that.activeName == "photo") {
                    console.log("hahah");
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.index') !!}",{}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            that.photolist = response.data.data.data;
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
                }
                if(that.activeName == "voice") {
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.index') !!}",{}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            console.log("hahahahah")
                            that.voicelist = response.data.data.data;
                            for(let i=0;i<that.voicelist.length;i++){
                                that.voicelist[i].createtime = new Date(parseInt(that.voicelist[i].createtime) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
                            }
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
                }
                if(that.activeName == "video") {
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.index') !!}",{}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            console.log("hahahahah")
                            that.videolist = response.data.data.data;
                            that.loading = false;
                            that.per_size = response.data.data.per_page;
                            that.total = response.data.data.total;
                            that.current_page = response.data.data.current_page;
                        }
                        that.loading = false;
                    }),function(res){
                        console.log(res);
                        that.loading = false;
                    };
                }
            },
            getNewsdata(){
                var that = this;
                that.loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.index') !!}",{}).then(response => {
                console.log(response);
                if(response.data.result==1){
                    console.log("hahahahah")
                    that.datas = response.data.data.data;
                    console.log(that.datas)
                    for(let i=0;i<that.datas.length;i++){
                        // that.datas[i].createtime = new Date(parseInt(that.datas[i].createtime) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
                    }
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
            // 上传图片之前
            beforeUpload(){
                this.loading=true;
            },
            // 上传图片成功之后
            uploadSuccess(response,file,fileList){
                if(response.result==1){
                    this.$message.success("上传成功！")
                    this.handleClick();
                }
                else{
                    this.$message.error(response.msg);
                }
                this.loading=false;
            },
            syncWechat() {
                console.log("lalal");
                var that = this;
                that.loading = true;
                var type = "";
                if(that.activeName=="image_text"){
                    type = "news";
                }
                else if(that.activeName=="photo"){
                    type = "image";
                }
                else if(that.activeName=="voice"){
                    type = "voice";
                }
                else if(that.activeName=="video"){
                    type = "video";
                }
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.sync-wechat.index') !!}",{type:type}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.material.controller.material.index') !!}';
                        that.loading = false;
                    }
                    else{
                        this.$message({message: response.data.msg,type: 'error'});
                        that.loading = false;
                    }
                }).catch(response => {
                    this.$message({message: response.data.msg,type: 'error'});
                    that.loading = false;
                })
            },
            // 删除素材
            del(type,id){
                this.$confirm('确定删除吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                this.loading=true;
                if(type=="news"){
                    this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.delete') !!}',{id:id}).then(function (response) {
                        if (response.data.result) {
                            this.$message({type: 'success',message: '删除成功!'});
                            this.currentChangeWechatTextImg(1);
                        }
                        else{
                            this.$message({type: 'error',message:response.data.msg });
                        }
                        this.loading=false;
                        },function (response) {
                            this.$message({type: 'error',message:response.data.msg });
                            this.loading=false;
                        }
                    );
                }
                if(type=="image"){
                    this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.delete') !!}',{id:id}).then(function (response) {
                        if (response.data.result) {
                            this.$message({type: 'success',message: '删除成功!'});
                            this.currentChangeWechatImg(1);
                        }
                        else{
                            this.$message({type: 'error',message:response.data.msg });
                        }
                        this.loading=false;
                        },function (response) {
                            this.$message({type: 'error',message:response.data.msg });
                            this.loading=false;
                        }
                    );
                }
                if(type=="voice"){
                    this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.delete') !!}',{id:id}).then(function (response) {
                        if (response.data.result) {
                            this.$message({type: 'success',message: '删除成功!'});
                            this.currentChangeWechatVoice(1);
                        }
                        else{
                            this.$message({type: 'error',message:response.data.msg });
                        }
                        this.loading=false;
                        },function (response) {
                            this.$message({type: 'error',message:response.data.msg });
                            this.loading=false;
                        }
                    );
                }
                if(type=="video"){
                    this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.delete') !!}',{id:id}).then(function (response) {
                        if (response.data.result) {
                            this.$message({type: 'success',message: '删除成功!'});
                            this.currentChangeWechatVideo(1);
                        }
                        else{
                            this.$message({type: 'error',message:response.data.msg });
                        }
                        this.loading=false;
                        },function (response) {
                            this.$message({type: 'error',message:response.data.msg });
                            this.loading=false;
                        }
                    );
                }
                
                }).catch(() => {
                    this.$message({type: 'info',message: '已取消删除'});
                    });
            },
            
            // 微信图文分页
            currentChangeWechatTextImg(val){
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.index') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.datas = response.data.data.data;
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
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.index') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.photolist = response.data.data.data;
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
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.index') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.voicelist = response.data.data.data;
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
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.index') !!}',{page:val}).then(function (response){
                    console.log(response);
                    this.videolist = response.data.data.data;
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
