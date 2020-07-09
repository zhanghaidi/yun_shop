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
</style>
<div class="rightlist">
    <div id="app" v-loading="submit_loading">
        <div class="rightlist-head">
            <div class="rightlist-head-con">公众号设置</div>
                <el-card shadow="always" style="border:1px solid #b3d8ff;color:#333;background:#ecf5ff;line-height:28px;font-weight:600;margin-bottom:50px;">
                    1.在此页面相应的输入框中填好信息<br>
                    #公众号名称、微信号、原始ID  注：以上信息可以通过<a href="https://mp.weixin.qq.com" target="_blank">微信公众号官方平台</a>设置--公众号设置--账号详情中获取。<br>
                    #开发者 (AppID)、 开发者密码(AppSecret)  注：以上信息可以通过<a href="https://mp.weixin.qq.com" target="_blank">微信公众号官方平台</a>开发--基本配置中获取。<br>
                    #令牌(ToKen)、 消息加密密钥（EncodingAESKey）注：点击生成新的或点击修改自己填写。<br>
                    #填写好的开发者 (AppID)、开发者密码(AppSecret)、令牌(ToKen)、 消息加密密钥（EncodingAESKey） 必须点击<span style="color: #6e2020; font-size: 20px">保存</span>！！<br>
                    2.在<a href="https://mp.weixin.qq.com" target="_blank">[开发者中心]</a>，找到［ 服务器配置 ］栏目下URL和Token设置<br>
                    #将[1]填写令牌(ToKen)、 消息加密密钥（EncodingAESKey）的填入对应输入框。<br>
                    # 如果以前已填写过URL和Token，请点击[ 修改配置 ] ，再填写上述链接。<br>
                    # 请点击 [ 提交 ] ,返回[提交成功]即为接入成功,注意是否有[提交成功提示，不然微信公众平台那边访问服务器拿不到数据就无法接入。<br>

                </el-card>
            <template>
                <el-form ref="form" :model="form" :rules="rules" label-width="15%">
                    <el-form-item label="启用公众号" prop="is_open">
                        <el-radio v-model.number="form.is_open" :label="1">开启</el-radio>
                        <el-radio v-model.number="form.is_open" :label="0">关闭</el-radio>
                        <div v-if="form.status==undefined" style="display:inline-block;width:100px;text-align:center;"><el-tag type="danger" style="margin-bottom:0 !important;">未接入</el-tag></div>
                        <span v-if="form.status==1" style="display:inline-block;width:100px;text-align:center;"><el-tag type="success" style="margin-bottom:0 !important;">已接入</el-tag></span>
                        <span v-if="form.status==0" style="display:inline-block;width:100px;text-align:center;"><el-tag type="danger" style="margin-bottom:0 !important;">未接入</el-tag></span>
                    </el-form-item>
                    <el-form-item label="公众号名称" prop="name">
                        <el-input v-model="form.name" style="width:70%" placeholder="请输入公众号名称"></el-input>
                    </el-form-item>
                    <el-form-item label="微信号" prop="wechat">
                        <el-input v-model="form.wechat" style="width:70%" placeholder="请输入微信号"></el-input>
                    </el-form-item>
                    <el-form-item label="公众号类型" prop="type">
                        <el-input value="认证服务号/认证媒体/政府订阅号" style="width:70%" disabled></el-input>
                    </el-form-item>
                    <el-form-item label="原始ID" prop="original_id">
                        <el-input v-model="form.original_id" style="width:70%" placeholder="请输入公众号原始ID"></el-input>
                        <div class="tip">注：以上信息可以通过微信公众号官方平台（https://mp.weixin.qq.com）设置--公众号设置--账号详情中获取。</div>
                    </el-form-item>
                    <el-form-item label="AppID" prop="app_id">
                        <el-input v-model="form.app_id" style="width:70%" placeholder="请输入AppID"></el-input>
                    </el-form-item>
                    <el-form-item label="AppSecret" prop="app_secret">
                        <el-input  v-model="form.app_secret" style="width:70%" placeholder="请输入AppSecret"></el-input>
                        <div class="tip">
                            注：以上信息可以通过微信公众号官方平台（https://mp.weixin.qq.com）开发--基本配置中获取。<br>
                            设置时务必同时设置好IP白名单，IP地址为您的服务器公网IP地址。
                        </div>
                    </el-form-item>
                    <el-form-item label="服务器地址(URL)" prop="address">
                        <el-input v-model="form.address" style="width:50%" readonly="readonly" ref="address"></el-input>
                        <el-button @click="copy(1)">复制</el-button>
                    </el-form-item>
                    <el-form-item label="令牌(ToKen)" prop="token">
                        <el-input v-model="form.token" style="width:50%" readonly="readonly" ref="token"></el-input>
                        <el-button @click="change(2)">修改</el-button>
                        <el-button @click="copy(2)">复制</el-button>
                        <el-button @click="newToken()">生成新的</el-button>
                    </el-form-item>
                    <el-form-item label="消息加密密钥（EncodingAESKey）" prop="aes_key">
                        <el-input v-model="form.aes_key" style="width:50%" readonly="readonly" ref="aes_key"></el-input>
                        <el-button @click="change(3)">修改</el-button>
                        <el-button @click="copy(3)">复制</el-button>
                        <el-button @click="newKey()">生成新的</el-button>
                    </el-form-item>
                    <el-form-item>
                        <a href="#">
                            <el-button type="success" @click="submitForm('form')">
                                保存
                            </el-button>
                        </a>
                    </el-form-item>
                </el-form>
            </template>
        </div>
        <el-dialog title="修改token" :visible.sync="dialogToken">
            <el-input type="text" v-model="new_token" style="width:100%" placeholder="请填写新的公众号消息检验Token">
            </el-input>
            <div class="tip">与公众平台接入设置值一致，必须为英文或者数字，长度为3到32个字符. 请妥善保管, Token 泄露将可能被窃取或篡改平台的操作数据.</div>
            <el-button @click="dialogToken = false">取 消</el-button>
            <el-button type="primary" @click="choose(2)">确 定</el-button>
        </el-dialog>
        <el-dialog title="修改EncodingAESKey" :visible.sync="dialogKey">
            <el-input type="text" v-model="new_key" style="width:100%" placeholder="请填写新的公众号消息加密Key">
            </el-input>
            <div class="tip">与公众平台接入设置值一致，必须为英文或者数字，长度为43个字符. 请妥善保管, EncodingAESKey 泄露将可能被窃取或篡改平台的操作数据.</div>
            <el-button @click="dialogKey = false">取 消</el-button>
            <el-button type="primary" @click="choose(3)">确 定</el-button>
        </el-dialog>
    </div>
</div>
    
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            let form1={!! $data !!};
            // let token ="{!! $token !!}";
            // let aes_key ="{!! $aes_key !!}";
            console.log(form1);

            // console.log(token);
            // console.log(aes_key)
            return{
                form1:form1,
                form:{
                    is_open:0,
                    name:"",
                    type:"",
                    original_id:"",
                    app_id:"",
                    app_secret:"",
                    address:"",
                    token:"",
                    aes_key:"",
                    ...form1,
                },
                // token:token,
                // aes_key:aes_key,
                submit_loading:false,
                new_token:"",
                dialogToken:false,
                new_key:"",
                dialogKey:false,
                rules:{
                    name:{required: true,message: '请输入公众号名称'},
                    wechat:{required: true,message: '请输入微信号'},
                    original_id:{required: true,message: '请输入原始ID'},
                    app_id:{required: true,message: '请输入AppID'},
                    app_secret:{required: true,message: '请输入AppSecret'},
                },
                
            }
        },
        created() {
            
        },
        methods: {
            copy(x) {
                that = this;
                if (x == 1){
                    let Url = that.$refs.address;
                    Url.select(); // 选择对象  
                    document.execCommand("Copy");
                    that.$message({message:"复制成功！",type:"success"});
                }
                if (x == 2){
                    let Url = that.$refs.token ;
                    Url.select(); // 选择对象  
                    document.execCommand("Copy");
                    that.$message({message:"复制成功！",type:"success"});
                }
                if (x == 3){
                    let Url = that.$refs.aes_key;
                    Url.select(); // 选择对象  
                    document.execCommand("Copy");
                    that.$message({message:"复制成功！",type:"success"});
                }
            },
            change(x) {
                if(x===2) {
                    this.dialogToken=true;
                }
                if(x===3) {
                    this.dialogKey=true;
                }
            },
            choose(x) {
                var that = this;
                if(x===2){
                    this.dialogToken=false;
                    if(!(new RegExp(/^[A-Za-z0-9]{3,32}$/)).test(that.new_token)){
                        this.$message({type: 'error',message: '必须为英文或者数字，长度为3到32个字符！'});
                        return false;
                    }
                    else {
                        this.$confirm('确定修改吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                            this.submit_loading = true;
                            this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.setting.setting.newToken') !!}',{token:that.new_token}).then(function (response) {
                            if (response.data.result) {
                                that.form.token = that.new_token;
                                this.$message({type: 'success',message: '修改成功!'});
                            }
                            else{
                                this.$message({type: 'error',message: response.data.msg});
                            }
                            this.submit_loading=false;
                            },function (response) {
                                this.submit_loading=false;
                            }
                            );
                            
                        }).catch(() => {
                            this.$message({type: 'info',message: '已取消修改！'});
                        });

                    }
                }
                if(x===3){
                    this.dialogKey=false;
                    if(!(new RegExp(/^[A-Za-z0-9]{43}$/)).test(that.new_key)){
                        this.$message({type: 'error',message: '必须为英文或者数字，长度为43个字符！'});
                        return false;
                    }
                    else {
                        this.$confirm('确定修改吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                            this.submit_loading = true;
                            this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.setting.setting.newKey') !!}',{aes_key:that.new_key}).then(function (response) {
                            if (response.data.result) {
                                that.form.aes_key = that.new_key;
                                this.$message({type: 'success',message: '修改成功!'});
                            }
                            else{
                                this.$message({type: 'error',message: response.data.msg});
                            }
                            this.submit_loading=false;
                            },function (response) {
                                this.submit_loading=false;
                            }
                            );
                            
                        }).catch(() => {
                            this.$message({type: 'info',message: '已取消修改！'});
                        });
                    }
                }
            },
            newKey(){
                var that = this;
                that.$confirm('确定生成新的吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                    that.submit_loading = true;
                    for (var e = "", t = 0; t < 43; t++){
                        e += "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" [parseInt(61 * Math.random() + 1)];
                    }
                    that.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.setting.setting.newKey') !!}',{aes_key:e}).then(function (response) {
                    if (response.data.result) {
                        that.form.aes_key = e;
                        console.log(e);
                        that.$message({type: 'success',message: '生成成功!'});
                    }
                    else{
                        that.$message({type: 'error',message: response.data.msg});
                    }
                    that.submit_loading=false;
                    },function (response) {
                        that.submit_loading=false;
                    }
                    );
                    
                }).catch(() => {
                    this.$message({type: 'info',message: '已取消生成！'});
                });
                
            },
            newToken() {
                var that = this;
                that.$confirm('确定生成新的吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                    that.submit_loading = true;
                    for (var e = "", t = 0; t < 32; t++){
                            e += "abcdefghijklmnopqrstuvwxyz0123456789" [parseInt(32 * Math.random())];
                        }
                        that.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.setting.setting.newToken') !!}',{token:e}).then(function (response) {
                    if (response.data.result) {
                        that.form.token = e;
                        console.log(e);
                        that.$message({type: 'success',message: '生成成功!'});
                    }
                    else{
                        that.$message({type: 'error',message: response.data.msg});
                    }
                    that.submit_loading=false;
                    },function (response) {
                        that.submit_loading=false;
                    }
                    );
                    
                }).catch(() => {
                    that.$message({type: 'info',message: '已取消生成！'});
                });
                
            },
            submitForm(formName) {
                console.log(this.form);
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.submit_loading = true;
                        // delete(this.form.token);
                        // delete(this.form.aes_key);
                        this.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.setting.setting.index') !!}",{'form_data':this.form}).then(response => {
                            if (response.data.result) {
                                this.$message({type: 'success',message: '操作成功!'});
                                window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.setting.setting.index') !!}';
                            } else {
                                this.$message({message: response.data.msg,type: 'error'});
                                this.submit_loading = false;
                            }
                        },response => {
                            this.$message({message: response.data.msg,type: 'error'});
                            this.submit_loading = false;
                        });
                    }
                    else {
                        return false;
                    }
                });
            },
            
        },
    })

</script>
@endsection
