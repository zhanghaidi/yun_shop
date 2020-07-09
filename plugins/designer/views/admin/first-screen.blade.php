@extends('layouts.base')
@section('title', '首屏广告')
@section('content')
    <link rel="stylesheet" href="{{static_url('css/public-number.css')}}">
    <style>
        .rightlist-screen #app .title{padding:15px 0;font-size:16px;line-height:30px;border-bottom:solid 1px #eee;color:#333;margin-bottom:25px;}
        .rightlist-screen #app .title b{margin-left:10px;}
        .rightlist-screen #app .rightlist-content .tab{margin-bottom:50px;}
        .rightlist-screen #app .rightlist-content .tab .tab-item{color:#333;padding:10px 0;width:100px;text-align:center;border:solid 1px #eee;border-radius:6px;margin-right:15px;display:inline-block;}
        .rightlist-screen #app .rightlist-content .switch{margin-left:15px;margin-bottom:30px;}
        .rightlist-screen #app .rightlist-content .switch .open-switch{margin-right:15px;width:100px;text-align:right;display:inline-block;}
        .rightlist-screen #app .rightlist-content .switch .already-open{margin-right:15px;display:inline-block}
        .rightlist-screen #app .rightlist-content .showTime{margin-left:15px;margin-bottom:30px;}
        .rightlist-screen #app .rightlist-content .showTime .advertise-showTime{margin-right:10px;width:100px;display:inline-block;text-align:right;}
        .rightlist-screen #app .rightlist-content .showTime .advertise-showTime-tab{color:#333;padding:10px 0;width:100px;text-align:center;border-radius:6px;margin-right:15px;display:inline-block;}
        .rightlist-screen #app .rightlist-content .rule{margin-left:15px;margin-bottom:30px;}
        .rightlist-screen #app .rightlist-content .rule .show-rule{margin-right:10px;width:100px;text-align:right;display:inline-block;}
        .rightlist-screen #app .rightlist-content .rule .rule-tab{color:#333;padding:10px 0;width:200px;text-align:center;border-radius:6px;margin-right:15px;display:inline-block;}
        .rightlist-screen #app .rightlist-content .advertise{margin-left:15px;margin-bottom:30px;}
        .rightlist-screen #app .rightlist-content .advertise .advertise-setting{margin-right:10px;width:100px;text-align:right;display:inline-block;vertical-align:top;}
        .rightlist-screen #app .rightlist-content .advertise .avatar-uploader{display:inline-block;}
        .rightlist-screen #app .rightlist-content .advertise .avatar-uploader .el-upload__input{display:none;}
        .rightlist-screen #app .rightlist-content .advertise .avatar-uploader .el-upload {border: 1px dashed #d9d9d9;border-radius: 6px;cursor: pointer;position: relative;overflow: hidden;}
        .rightlist-screen #app .rightlist-content .advertise .avatar-uploader .el-upload:hover {border-color: #409EFF;}
        .rightlist-screen #app .rightlist-content .advertise .avatar-uploader-icon {font-size: 28px;color: #8c939d;width:300px;height: 300px;line-height: 300px;text-align: center;}
        .rightlist-screen #app .rightlist-content .advertise .avatar {width:300px;height:300px;display:block;}
        .rightlist-screen #app .rightlist-content .advertise .change-advertise{display:inline-block;vertical-align:top;margin-left:15px;}
        .rightlist-screen #app .rightlist-content .advertise .change-advertise .change-advertise-img{display:inline-block;width:150px;padding:10px 0;background-color:#d9534f;color:#fff;border-radius:6px;text-align:center;font-size:18px;position:relative;}
        .rightlist-screen #app .rightlist-content .advertise .change-advertise .change-advertise-img :hover{cursor:pointer;}
        .rightlist-screen #app .rightlist-content .advertise .change-advertise .change-advertise-img input{ position: absolute;top: 0;height: 100%;width: 100%;z-index: 999;opacity: 0;}
        .rightlist-screen #app .rightlist-content .advertise .change-advertise .choose-link{display:block;width:150px;padding:10px 0;background-color:#d9534f;color:#fff;border-radius:6px;text-align:center;font-size:18px;margin-top:15px;}
        .rightlist-screen #app .rightlist-content .advertise .change-advertise .choose-link:hover{cursor:pointer;}
        .rightlist-screen #app .rightlist-content .advertise .change-advertise .program-link{display:block;width:150px;padding:10px 0;background-color:#d9534f;color:#fff;border-radius:6px;text-align:center;font-size:18px;margin-top:15px;}
        .rightlist-screen #app .rightlist-content .advertise .change-advertise .program-link:hover{cursor:pointer;}
        .rightlist-screen #app .rightlist-content .advertise  .change-advertise .change-advertise-tip{display:block;margin-top:15px;}
        .rightlist-screen #app .rightlist-content .advertise  .tip{margin-left:123px;}
        .rightlist-screen #app .rightlist-content .advertise  .change-advertise .local-link{display:block;margin-top:5px;}
        .rightlist-screen #app .rightlist-content .rightlist-content-btn{margin-left:500px;color:#fff;background-color:#d9534f;border-radius:6px;width:150px;padding:10px 0;text-align:center;font-size:18px;}
        .rightlist-screen #app .rightlist-content .rightlist-content-btn:hover{cursor:pointer;}
        .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
        .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
        .tabActive{background-color:#8bc34a;}
        .showTimeTab{border:solid 1px #d9534f;} 
        .showTimeNor{border:solid 1px #eee;}
        .ruleNor{border:solid 1px #eee;}
        .ruleTabActive{border:solid 1px #d9534f;}
    </style>
    <div class="rightlist-screen">
        <div id="app">
            <div class="title">
                <b>首屏广告</b>
            </div>
            <div class="rightlist-content">
                <div class="tab">
                    <span class="tab-item" @click="form.tabID=0" :class="{tabActive:form.tabID==0}">全屏广告</span> 
                    <span class="tab-item" @click="form.tabID=1" :class="{tabActive:form.tabID==1}">弹窗广告</span>
                </div>   
                <div class="switch" v-show="form.tabID==0">
                    <span class="open-switch">开启/关闭:</span>
                     <el-switch
                        v-model="form.value"
                        active-color="#05be03"
                        >
                    </el-switch>
                    <span v-show="form.value" class="already-open">已开启</span>
                    <span>需保存生效，启用后用户进入商城后，全屏广告图将覆盖首页1-3秒，可作为品牌宣传启动页使用</span>
                </div> 
                <div class="switch" v-show="form.tabID==1">
                    <span class="open-switch">开启/关闭:</span>
                     <el-switch
                        v-model="form.popValue"
                        active-color="#05be03"
                        >
                    </el-switch>
                    <span v-show="form.popValue" class="already-open">已开启</span>
                    <span>需保存生效，启用后用户商城后，显示弹窗，弹窗需用户主动关闭，可作为广告营销使用</span>
                </div> 
                <div class="showTime" v-show="form.tabID==0">
                    <span class="advertise-showTime">广告显示时间:</span>
                    <span class="advertise-showTime-tab " @click="form.TimeID=1" :class="[form.TimeID==1 ? 'showTimeTab' : 'showTimeNor']">1秒</span>
                    <span class="advertise-showTime-tab"  @click="form.TimeID=2" :class="[form.TimeID==2 ?  'showTimeTab' : 'showTimeNor']">2秒</span>
                    <span class="advertise-showTime-tab"  @click="form.TimeID=3" :class="[form.TimeID==3 ?  'showTimeTab' : 'showTimeNor']">3秒</span>
                <span>
                </div>
                <div class="rule" v-show="form.tabID==0">
                    <span class="show-rule">显示规则:</span> 
                    <span class="rule-tab"  @click="form.ruleID=0" :class="[form.ruleID==0 ? 'ruleTabActive' : 'ruleNor']">每次进入首页时显示</span>
                    <span class="rule-tab"  @click="form.ruleID=1" :class="[form.ruleID==1 ? 'ruleTabActive' : 'ruleNor']">每日首次登录小程序时显示</span>
                </div>
                <div class="rule" v-show="form.tabID==1">
                    <span class="show-rule">显示规则:</span> 
                    <span class="rule-tab"  @click="form.popID=0;" :class="[form.popID==0 ? 'ruleTabActive' : 'ruleNor']">每次进入首页时显示</span>
                    <span class="rule-tab"  @click="form.popID=1;" :class="[form.popID==1 ? 'ruleTabActive' : 'ruleNor']">每日首次登录小程序时显示</span>
                </div>
                <div class="advertise" v-show="form.tabID==0">
                    <span class="advertise-setting">广告图设置:</span>
                    <el-upload
                        class="avatar-uploader"
                        :action="uploadUrl"
                        :show-file-list="false"
                        :on-success="indexSuccess"
                        :before-upload="indexUpload">
                        <img v-if="form.imageUrl" :src="form.imageUrl" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                    <div class="change-advertise">
                        <div class="change-advertise-img" >更换图片<input type="file" ref="upLoad" @change="changePiture"></div>
                        <span class="change-advertise-tip" >建议尺寸750px*1206px,大小限制在500k以内;</span>
                        <span class="change-advertise-tip">在保证图片清晰的情况下，请尽可能压缩图片大小，以免图片过大导致图片加载太慢，影响用户体验。</span>
                    </div>
                    <div class="rightlist-content-btn" @click="Save">
                        <span>保存</span>
                    </div>
                </div>
                <div class="advertise" v-show="form.tabID==1">
                    <span class="advertise-setting">广告图设置:</span>
                    <el-upload
                        class="avatar-uploader"
                        :action="uploadUrl"
                        :on-success="popSuccess"
                        :show-file-list="false"
                        :before-upload="popUpload">
                        <img  v-if="form.popImage" :src="form.popImage" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                    <div class="change-advertise">
                        <div class="change-advertise-img" >更换图片<input type="file" ref="popLoad" @change="popPiture"></div>
                        <span class="choose-link" @click="show=true" v-if="!form.popID==1">选择公众号链接</span>
                        <span class="program-link" @click="pro=true">选择小程序链接</span>
                        <span class="local-link" v-if="form.link&&form.popID==0">当前公众号链接：[[form.link]]</span>
                        <span class="pro-link" v-if="form.prolink">当前小程序链接：[[form.prolink]]</span>
                    </div>
                    <div class="tip">
                        <span>建议尺寸,宽度560px,高度750px,大小限制在500k</span> 
                    </div>
                    <div class="rightlist-content-btn" @click="Save">
                        <span>保存</span>
                    </div>
                </div>
            </div>
            <pop :show="show" @replace="changeProp" @add="parHref"></pop>
            <program :pro="pro" @replacepro="changeprogram" @addpro="parpro"></program>
        </div>
    </div> 
      @include('public.admin.pop')  
      @include('public.admin.program')
    <script>
        var vm = new Vue({
            el:"#app",
            delimiters: ['[[', ']]'],
                data() {
                    let upload = '{!! $uploadurl?:'{}' !!}';
                    let advertisement_data=JSON.parse('{!! $advertisement_data?:'{}' !!}');
                    if(advertisement_data===null){
                        advertisement_data={
                            image: "",
                            Midimage:'',
                            link: "",
                            prolink: "",
                            rule: 0,
                            Midrule:0,
                            switch: false,
                            Midswitch:false,
                            time: 1,
                            type:0,
                        }
                    }
                    return{
                        uploadUrl:upload,
                        show:false,//是否开启公众号弹窗
                        pro:false ,//是否开启小程序弹窗 
                        form:{
                            value: advertisement_data.switch,  //控制全屏广告按钮的开启和关闭
                            popValue:advertisement_data.Midswitch, //控制弹窗广告按钮的开启和关闭
                            tabID:advertisement_data.type, //控制选择全屏广告还是弹窗广告
                            TimeID:advertisement_data.time, //控制广告的显示时间
                            ruleID:advertisement_data.rule, //全屏广告显示规则的控制
                            popID:advertisement_data.Midrule, //弹窗广告显示规则的控制
                            imageUrl:advertisement_data.image,//全屏广告的图片路径
                            popImage:advertisement_data.Midimage,//弹窗广告的图片路径
                            link:advertisement_data.link,//当前公众号链接
                            prolink:advertisement_data.prolink, //当前小程序链接
                            fullImage:advertisement_data.image,//全屏图片链接
                            nofull:advertisement_data.Midimage//弹窗图片链接
                        }
                    }
                },
                mounted: function () {
                },
                methods: {
                    //初始化参数
                    //首页广告图片上传成功的回调
                    indexSuccess(res,file){
                        this.form.fullImage=res.url;
                        this.form.imageUrl = URL.createObjectURL(file.raw);
                    },
                    //首页广告图片上传
                    indexUpload(file){
                        const isImg = file.type === 'image/jpeg' || file.type==="image/png";
                        const isLt500K = file.size / 1024 <500;
                        if (!isImg) {
                            this.$message.error('上传图片的格式只能是 JPG或PNG 格式!');
                            return false;
                        }
                        if (!isLt500K) {
                            this.$message.error('上传图片大小不能超过 500K!');
                            return false;
                        }
                    },
                    //弹窗广告图片上传成功的回调
                    popSuccess(res,file){
                        this.form.nofull=res.url;
                        this.form.popImage = URL.createObjectURL(file.raw);
                    },
                    //弹窗广告图片上传
                    popUpload(file) {
                        const isImg = file.type === 'image/jpeg' || file.type==="image/png";
                        const isLt500K = file.size / 1024 <500;
                        if (!isImg) {
                            this.$message.error('上传图片的格式只能是 JPG或PNG 格式!');
                            return false;
                        }
                        if (!isLt500K) {
                            this.$message.error('上传图片大小不能超过 500K!');
                            return  false ;
                        }
                    },
                    //全屏广告更换图片
                    changePiture(){
                        let file=this.$refs.upLoad.files[0];
                        const isImg = file.type === 'image/jpeg' || file.type==="image/png";
                        const isLt500K = file.size / 1024 <500;
                        if (!isImg) {
                            this.$message.error('上传图片的格式只能是 JPG或PNG 格式!');
                            return false;
                        }
                        if (!isLt500K) {
                            this.$message.error('上传图片大小不能超过 500K!');
                            
                            return false;
                        }
                        let fd= new FormData();
                        fd.append("file", file);
                        this.$http.post(this.uploadUrl,fd).then(function (response){
                                this.form.fullImage=response.data.url;
                                this.form.imageUrl =response.data.url;
                            },function (response) {
                                console.log(response);
                            }
                        );
                    },
                    //弹窗广告更换图片
                    popPiture(){
                        let file=this.$refs.popLoad.files[0];
                        const isImg = file.type === 'image/jpeg' || file.type==="image/png";
                        const isLt500K = file.size / 1024 <500;
                        if (!isImg) {
                            this.$message.error('上传图片的格式只能是 JPG或PNG 格式!');
                            return false;
                        }
                        if (!isLt500K) {
                            this.$message.error('上传图片大小不能超过 500K!');
                            return false;
                        }
                        let fd= new FormData();
                        fd.append("file", file);
                        this.$http.post(this.uploadUrl,fd).then(function (response){
                                this.form.nofull=response.data.url;
                                this.form.popImage =response.data.url;
                            },function (response) {
                                console.log(response);
                            }
                        );
                    },
                    //保存信息
                    Save(){
                        let json={
                            type:this.form.tabID,
                            switch:this.form.value,
                            Midswitch:this.form.popValue,
                            time:this.form.TimeID,
                            rule:this.form.ruleID,
                            Midrule:this.form.popID,
                            image:this.form.fullImage,
                            Midimage:this.form.nofull,
                            link:this.form.link,
                            prolink:this.form.prolink
                        }
                        if(json.switch){
                            if(json.image==''){
                                this.$message.error('请上传全屏广告图片');
                                return 
                            }
                        }
                        if(json.Midswitch){
                            if(json.Midimage==''){
                                this.$message.error('请上传弹窗广告图片');
                                return 
                            }
                        }
                            this.$http.post('{!! yzWebFullUrl('plugin.designer.admin.first_screen.index') !!}',{'form_data':json}).then(function (response){
                                this.$message({message:"保存成功",type:"success"});
                            },function (response) {
                                console.log(response);
                            }
                        );
                    },
                    //弹窗显示与隐藏的控制
                    changeProp(item){
                        this.show=item;
                    },
                    //当前链接的增加
                    parHref(child,confirm){
                        this.show=confirm;
                        this.form.link=child;

                    },
                    changeprogram(item){
                        this.pro=item;
                    },
                    parpro(child,confirm){
                        this.pro=confirm;
                        this.form.prolink=child;
                    }
                },
            });
    </script>
   
    
@endsection