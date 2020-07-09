@extends('layouts.base')
@section('title', trans('基础设置'))
@section('content')

    
    <!-- <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-heading'>客户端设置</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">版本号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="applet[version]" class="form-control" placeholder="1.1.1"/>
                                <span class="help-block">版本号仅限数字，例：1.1.1</span>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">版本描述</label>
                            <div class="col-sm-9 col-xs-12">
                                <textarea name="applet[describe]" rows="5" class="form-control" placeholder="本次上传的版本备注"></textarea>
                                <span class="help-block">本次上传的版本备注，允许100个字符。</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>
                    </div>
            </form>
        </div>
    </div> -->
    <style>
        
        .right-content1 {
            width: 80%;
            margin: 0 auto;
        }
        .step-head {
            font-size: 35px;
            font-weight: 900;
            text-align: center;
            padding: 50px 0;
        }
        .right-content-main1 {
            margin-top: 30px;
            font-size: 18px;
        }
        .tip{
            color:#999;font-size:12px;
        }
    </style>
    <div id="app" v-loading="submit_loading">
        <div>
            <div class="step-head">
                <div v-show="active == 1">1 填写信息</div>
                <div v-show="active == 2">2 扫码并上传代码</div>
                <div v-show="active == 3">3 上传成功</div>
                
            </div>
            <div class="rxight-content1">
                <el-steps :active="active" align-center space="100%">
                    <el-step title="1 填写信息" icon="el-icon-menu"></el-step>
                    <el-step title="2 扫码并上传代码" icon="el-icon-s-promotion"></el-step>
                    <el-step title="3 上传成功" icon="el-icon-finished"></el-step>
                </el-steps>
                <div v-show="active == 1" style="padding-top:50px;">
                    <el-form ref="form" :model="form" :rules="rules" label-width="25.5%">
                        <el-form-item label="版本号" prop="version">
                            <el-input v-model="form.version" style="width:70%"></el-input>
                            <div class="tip">版本号仅限数字，例：1.1.1</div>
                        </el-form-item>
                        <el-form-item label="版本描述" prop="description">
                            <el-input type="textarea" v-model="form.description" style="width:70%" rows="5"></el-input>
                            <div class="tip">本次上传的版本备注，允许100个字符。</div>
                        </el-form-item>
                        
                        <el-form-item label="选择版本" prop="">
                            <el-radio v-model.number="form.type" :label="0">不含小程序直播</el-radio>
                            <el-radio v-model.number="form.type" :label="1">含小程序直播</el-radio>
                            <div class="tip" style="color:red;">
                                注意：如果您小程序官方后台功能中不具备小程序直播功能，并且设置--第三方设置--插件管理中添加了小程序直播插件，请选择不含小程序直播版本，否则将无法上传代码！
                            </div>
                        </el-form-item>
                        <el-form-item label="" prop="">
                            
                            <el-button
                                type="primary"
                                @click="submitForm('form')"
                                v-show="active == 1"
                                >提交</el-button>
                        </el-form-item>
                    </el-form>
                
                </div>
                <div v-show="active == 2" style="padding-top:50px;">
                    <div v-if="is_waitting==1" style="text-align:center;font-size:14px;font-weight:600;line-height:60px;">
                        <div style="font-size:120px">
                            <i class="el-icon-time"></i>
                        </div>
                        <div>
                            您前面有[[count]]位客户在等待上传，预计需要等待[[time]]秒。
                        </div>
                        <div>
                            二维码返回扫码确认时间<span style="color:red">只限30秒</span>，即将排队到您时，请提前打开小程序管理员微信--扫一扫！过期您必须重新排队！
                        </div>
                    </div>
                    <div style="text-align:center" v-if="is_waitting==0">
                        <img v-if="qr_code" :src="qr_code" alt="" style="width:150px;height:150px;">
                        <div style="font-size:14px;font-weight:600;line-height:60px;">请扫码二维码，确认后将直接上传代码</div>
                        <div style="font-size:17px;color:red;font-weight:800">
                            [[count_down]]  S
                        </div>
                        <div style="font-size:14px;font-weight:600;line-height:60px;">管理员扫码点击确认后，请等待3-5秒确认上传结果</div>
                    </div>
                    <!-- 超时 -->
                    <div v-if="is_waitting==-1" style="text-align:center;font-size:14px;font-weight:600;line-height:60px;">
                        <div style="font-size:120px">
                            <i class="el-icon-error"></i>
                        </div>
                        <div>
                            扫码超时，请您重新刷新页面排队上传！
                        </div>
                    </div>
                    <!-- 接口出错时 -->
                    <div v-if="is_waitting==2" style="text-align:center;font-size:14px;font-weight:600;line-height:60px;">
                        <div style="font-size:120px">
                            <i class="el-icon-error"></i>
                        </div>
                        <div>
                            [[message]]
                        </div>
                        <div>
                            <el-button @click="goBack">返回重新填写</el-button>
                        </div>
                    </div>
                    <!-- 上传中 -->
                    <div v-if="is_waitting==3" style="text-align:center;font-size:14px;font-weight:600;line-height:60px;">
                        <div style="font-size:120px">
                            <i class="el-icon-time"></i>
                        </div>
                        <div>
                            上传中，请耐心等待上传结果
                        </div>
                    </div>
                
                </div>
                <div v-show="active == 3" style="text-align:center;padding-top:30px;">
                    <!-- <div
                        class=""
                        style="font-size:50px;text-align:center;color:#409eff;padding:100px 0;font-weight:900"
                    >
                        <i class="el-icon-success"> 上传成功</i>
                    </div> -->
                    <div style="font-size:36px;text-align:center;padding-top:30px">
                        <i class="el-icon-success" style="color:#409eff;"></i>
                    </div>
                    <div style="font-weight:800;line-height:48px;font-size:15px">
                        上传代码成功，请到微信开发平台小程序后台预览，提交审核应用。
                    </div>
                    <div style="font-weight:600;line-height:48px;">
                        微信开发平台小程序后台<a style="color:#409eff;" href="https://mp.weixin.qq.com/" target="_blank">https://mp.weixin.qq.com/</a>
                    </div>
                    <a href="https://mp.weixin.qq.com/" target="_blank">
                        <el-button>去提交审核</el-button>
                    </a>
                </div>
                
                <div class="right-content-btn">
                    
                
                </div>
            </div>
        </div>
    </div>



    <script>
        var app = new Vue({
            el: '#app',
            delimiters: ['[[', ']]'],
            data() {
                
                
                return {
                    submit_loading:false,
                    form:{
                        type:0,
                    },
                    identifier:'',
                    qr_code:'',
                    active:1,
                    count_down:30,//倒计时
                    is_waitting:0,//是否需要排队
                    count:0,//排队数
                    time:0,//等待几秒
                    real_time:0,
                    message:'',
                    is_scan_success:0,
                    rules: {
                        version: [
                            { required: true,message: '请输入版本号', trigger: 'blur' },
                        ],
                        description: [
                            { required: true,message: '请输入版本描述', trigger: 'blur' },
                        ],
                    },

                    

                }
            },
            mounted: function () {
                console.log(this.form)
                // this.count()
            },
            methods: {
                
                change(type) {
                    this.active = 2;
                },
                submitForm(formName) {
                    let that = this;
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            this.submit_loading = true;
                            this.$http.post("{!! yzWebFullUrl('plugin.min-app.Backend.Modules.Manual.Controllers.login.index') !!}",{version:this.form.version,description:this.form.description,type:this.form.type}).then(response => {
                                if (response.data.result) {
                                    // this.$message({type: 'success',message: '操作成功!'});
                                    // window.location.href='{!! yzWebFullUrl('plugin.asset.Backend.Modules.Category.Controllers.records') !!}';
                                    // 不需等待
                                    if(response.data.data.identifier) {
                                        that.is_waitting = 0;
                                        that.identifier = response.data.data.identifier;
                                        that.qr_code = response.data.data.qr_code;
                                        that.active = 2;
                                        this.count1();
                                        this.submit_loading = false;
                                        setTimeout(function(){
                                            that.isScan();
                                        }, 3000);
                                    }
                                    // 需要等待
                                    else {
                                        that.count = response.data.data.count;
                                        that.time =  response.data.data.time;
                                        that.real_time = that.time*1000;
                                        that.active = 2;
                                        that.is_waitting = 1;
                                        this.submit_loading = false;
                                        this.count2();
                                        //请求等待接口
                                        
                                    }
                                    
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
                isScan() {
                    let that = this;
                    this.$http.post("{!! yzWebFullUrl('plugin.min-app.Backend.Modules.Manual.Controllers.output.index') !!}",{identifier:that.identifier}).then(response => {
                        // 是否扫码登录，三个状态：继续等待（定时请求），超时（显示超时页面），已登录（请求是否上传成功接口）
                        if (response.data.result) {
                            if(response.data.data.status == 'SUCCESS') {
                                that.is_scan_success = 1;
                                that.is_waitting = 3;
                                that.isSuccess();
                            }
                            else if(response.data.data.status == 'WAIT')
                                if(that.count_down >=2) {
                                    setTimeout(function(){
                                        that.isScan();
                                    }, 3000);
                                }
                        } else {
                            // that.$message.error(response.data.msg)
                            that.is_waitting = 2;
                            that.message = response.data.msg
                        }
                    },response => {
                    });
                },
                isSuccess() {
                    let that = this;
                    // that.submit_loading = true;
                    this.$http.post("{!! yzWebFullUrl('plugin.min-app.Backend.Modules.Manual.Controllers.upload.index') !!}",{identifier:that.identifier}).then(response => {
                        if (response.data.result) {
                            // that.submit_loading = false;
                            if(response.data.data.status == 'SUCCESS') {
                                that.active = 3;
                            }
                            else if(response.data.data.status == 'WAIT'){
                                // that.is_waitting = 3;
                                setTimeout(function(){
                                    that.isSuccess();
                                }, 3000);
                            }
                        } else {
                            // that.submit_loading = false;
                            // that.$message.error(response.data.msg)
                            that.is_waitting = 2;
                            that.message = response.data.msg
                        }
                    },response => {
                    });
                },
                goBack() {
                    let that = this;
                    this.active = 1;
                    this.is_waitting = 0;
                    this.count_down = 30;
                    this.is_scan_success = 0;
                },
                count1() {
                    let that = this;
                    if(that.is_scan_success == 1) {
                        return;
                    }
                    console.log(this.count_down)
                    this.count_down--;
                    if(this.count_down<=0) {
                        this.count_down = 0;
                        that.is_waitting = -1;
                        return;
                    }
                    setTimeout(() => {
                        this.count1()
                    }, 1000);
                },
                count2() {
                    let that = this;
                    that.time--;
                    if(that.time<=0) {
                        that.time = 0;
                        // that.is_waitting = -1;
                        that.submitForm('form');
                    }
                    else {
                        setTimeout(() => {
                            this.count2()
                        }, 1000);

                    }
                }
                

            }
        });
    </script>
@endsection


