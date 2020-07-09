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

    .voice_con{width:300px;height:50px;overflow:hidden;position:relative;}
    .voice_con img{width:50px;height:50px;}
    .voice_con_time{display:inline-block;line-height:50px;margin-left:20px}

</style>

<div class="rightlist">
    <div id="app" v-loading="loading">
    <link rel="stylesheet" href="//at.alicdn.com/t/font_432132_v610m1e8re.css">
        <div class="rightlist-head">
            <div class="rightlist-head-con">公众号素材</div>
            <div class="text-align:right"><el-button type="primary" @click="syncWechat()">同步公众号素材</el-button></div>

        </div>
        <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
            <el-tab-pane label="图文" name="image_text">
                <div class="image_text">
                    <el-row>
                        <el-col :span="6" style="margin:10px 30px;width:400px" v-for="(item,index) in datas" :key="index">
                            <div class="image_text_head">
                                <div style="width:100%;">
                                    <div class="image_text_head_time">
                                        <div>
                                            <div style="width:80%;display:inline-block;">[[item.createtime]]</div>
                                            <i class="iconfont icon-pay_b" style="float:right;display:inline-block;width:30px;height:30px;color:green;"></i>
                                            
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
                                        <img style="width:80px;height:80px;" :src="list.url" alt="">
                                    </div>
                                </div>
                            </div>
                        </el-col>
                    </el-row>
                </div>
            </el-tab-pane>
            <el-tab-pane label="图片" name="photo">
                <el-row>
                    <el-col :span="5" style="margin:10px 30px;width:330px;" v-for="(item,index) in photolist" :key="index">
                        <div class="image_text_head">
                            <div style="width:100%;">
                                <div class="image_text_head_time">
                                    <div>
                                        <div style="width:80%;display:inline-block;overflow:hidden;">[[item.filename]]</div>
                                        <i class="iconfont icon-pay_b" style="float:right;display:inline-block;width:30px;height:30px;color:green;"></i>
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
            </el-tab-pane>
            
            <el-tab-pane label="语音" name="voice">
                <el-row>
                    <el-col :span="5" style="margin:10px 30px;width:330px;" v-for="(item,index) in voicelist" :key="index">
                        <div class="image_text_head">
                            <div style="width:100%;">
                                <div class="image_text_head_time">
                                    <div>
                                        <div style="width:80%;display:inline-block;overflow:hidden;">[[item.addtime]]</div>
                                        <i class="iconfont icon-pay_b" style="float:right;display:inline-block;width:30px;height:30px;color:green;"></i>
                                    </div>
                                </div>
                            </div>
                            <div style="margin:20px 30px">
                                <div class="voice_con">
                                    <!-- <img :src="item.url"  alt=""> -->
                                    <img src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548933239925&di=9886b3b1c6320025d811bba9d626a592&imgtype=0&src=http%3A%2F%2Fpic.58pic.com%2F58pic%2F13%2F57%2F65%2F79q58PICgsd_1024.jpg" alt="">
                                    <div class="voice_con_time">[[item.time_length]]</div>
                                </div>
                            </div>
                        </div>
                    </el-col>
                </el-row>
            </el-tab-pane>
            <el-tab-pane label="视频" name="video">
                <el-row>
                    <el-col :span="5" style="margin:10px 30px;width:330px;" v-for="(item,index) in videolist" :key="index">
                        <div class="image_text_head">
                            <div style="width:100%;">
                                <div class="image_text_head_time">
                                    <div>
                                        <div style="width:80%;display:inline-block;overflow:hidden;">[[item.url_name]]</div>
                                        <i class="iconfont icon-pay_b" style="float:right;display:inline-block;width:30px;height:30px;color:green;"></i>
                                    </div>
                                </div>
                            </div>
                            <div style="margin:10px">
                                <div class="photo_text_con">
                                    <img :src="item.url"  alt="">
                                </div>
                            </div>
                        </div>
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
            let data = {!! $data !!};
            console.log(data);
            for(let i=0;i<data.length;i++){
                data[i].createtime = new Date(parseInt(data[i].createtime) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
            }
            return{
                activeName:"image_text",
                datas:data,
                loading:false,
                photolist:"",
                voicelist:"",
                videolist:"",

                // datas :[
                //     {addtime:"2018-12-18",list:[
                //         {id:1,title:"【芸众商城测试】新功能测试中1-1...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                        
                //     ]},
                //     {addtime:"2018-12-18",list:[
                //         {id:1,title:"【芸众商城测试】新功能测试中2-1...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中2-2...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中2-3...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中2-4...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},                        
                //     ]},
                //     {addtime:"2018-12-18",list:[
                //         {id:1,title:"【芸众商城测试】新功能测试中3-1...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中3-2...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中3-3...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中3-4...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},                        
                //     ]},
                //     {addtime:"2018-12-18",list:[
                //         {id:1,title:"【芸众商城测试】新功能测试中3-5...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中3-6...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中3-7...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //         {id:1,title:"【芸众商城测试】新功能测试中3-8...",url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},                        
                //     ]},
                    
                // ],
                
                // photolist:[
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548933239925&di=1588f0b836371fdc53644dc70b965cb4&imgtype=0&src=http%3A%2F%2Fpic38.nipic.com%2F20140212%2F17942401_101320663138_2.jpg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548933239925&di=9886b3b1c6320025d811bba9d626a592&imgtype=0&src=http%3A%2F%2Fpic.58pic.com%2F58pic%2F13%2F57%2F65%2F79q58PICgsd_1024.jpg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                    
                // ],
                // voicelist:[
                //     {id:1,addtime:"2018-12-11",time_length:"3:20"},
                //     {id:1,addtime:"2018-12-12",time_length:"3:20"},
                //     {id:1,addtime:"2018-12-13",time_length:"3:20"},
                //     {id:1,addtime:"2018-12-14",time_length:"3:20"},
                //     {id:1,addtime:"2018-12-15",time_length:"3:20"},
                //     {id:1,addtime:"2018-12-16",time_length:"3:20"},
                // ],
                // videolist:[
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548933239925&di=1588f0b836371fdc53644dc70b965cb4&imgtype=0&src=http%3A%2F%2Fpic38.nipic.com%2F20140212%2F17942401_101320663138_2.jpg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548933239925&di=9886b3b1c6320025d811bba9d626a592&imgtype=0&src=http%3A%2F%2Fpic.58pic.com%2F58pic%2F13%2F57%2F65%2F79q58PICgsd_1024.jpg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                //     {id:1,url:"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1548927137553&di=00831232bbd6ea1db329dc10d9d96332&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg",url_name:"com%2Fuploads%2Fitem%2F201210%2F06%2F20121006120433_CZXuC.jpeg"},
                    
                // ],
                rules:{},
            }
        },
        methods: {
            handleClick(tab, event) {
                var that = this;
                that.loading = true;
                console.log(tab.name);
                if(tab.name == "photo") {
                    console.log("hahah");
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.index') !!}",{}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            console.log("hahahahah")
                            that.photolist = response.data.data.data;
                            that.loading = false;
                        }
                        that.loading = false;
                    }),function(res){
                        console.log(res);
                        that.loading = false;
                    };
                }
                if(tab.name == "voice") {
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.voice.index') !!}",{}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            console.log("hahahahah")
                            that.voicelist = response.data.data.data;
                            that.loading = false;
                        }
                        that.loading = false;
                    }),function(res){
                        console.log(res);
                        that.loading = false;
                    };
                }
                if(tab.name == "video") {
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.video.index') !!}",{}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            console.log("hahahahah")
                            that.voicelist = response.data.data.data;
                            that.loading = false;
                        }
                        that.loading = false;
                    }),function(res){
                        console.log(res);
                        that.loading = false;
                    };
                }
                if(tab.name=="image_text"){
                    that.loading = false;
                }
            },
            syncWechat() {
                console.log("lalal");
                var that = this;
                that.loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.sync-wechat.index') !!}",{}).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.index') !!}';
                        that.loading = false;
                    }
                    else{
                        this.$message({message: response.data.msg,type: 'error'});
                        that.loading = false;
                    }
                })
            }
        },
    })

</script>
@endsection
