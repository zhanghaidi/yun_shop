@extends('layouts.base')
@section('title', "基础设置")
@section('content')

    <style>
        .rightlist #app .rightlist-head{padding:15px 0;line-height:50px;border-bottom:1px solid #ccc;}
        .rightlist #app{margin-left:30px;}
        .rightlist-head-con{float:left;padding-right:20px;font-size:16px;color:#888;}
        .el-form-item__label{padding-right:30px;}
        .mouse-active{cursor:pointer;border:1px dotted #409EFF;border-radius: 4px;}
        /* 滑块选择小白点 */
        .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
        .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
        
        .avatar-uploader .el-upload {margin-top:15px;border: 1px dashed #d9d9d9;border-radius: 6px;cursor: pointer;position: relative;overflow: hidden;}
        .avatar-uploader .el-upload:hover {border-color: #409EFF;}
        .avatar-uploader-icon {font-size: 28px;color: #8c939d;width: 178px;height: 178px;line-height: 178px;text-align: center;}
        .avatar {width: 178px;height: 178px;display: block;}
        .el-upload-tip{width:178px;margin:0;padding:0;color:#999;text-align:center;}
        input[type=file] {display: none;}
        .avatar-uploader-box{position:relative;width:200px;}
        .el-icon-circle-close{position:absolute;top:10px;right:0;color:#999;}
        .tip{font-size:12px;color:#999;}
    </style>

    <div class="rightlist">
        <div id="app" v-loading="loading">
            <el-form ref="form" :model="form" :rules="rules" ref="form" label-width="240px">
                <h5 class="rightlist-head">
                    基础设置
                </h5>
                <el-form-item label="开启插件" prop="is_open">
                    <el-radio v-model.number="form.is_open" :label="1">开启</el-radio>
                    <el-radio v-model.number="form.is_open" :label="0">关闭</el-radio>
                </el-form-item>
                <el-form-item label="appId" prop="appId" style="align-content: center;width: 700px">
                    <el-input v-model="form.appId" placeholder="小程序appId"></el-input>
                </el-form-item>
                <el-form-item label="secret" prop="secret" style="align-content: center;width: 700px">
                    <el-input v-model="form.secret" placeholder="小程序secret"></el-input>
                </el-form-item>

                <el-form-item label="" prop="">
                    <div class="tip">直播间房间的链接 plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin?room_id=${roomId}（把${roomId}换成房间号，房间号可在小程序后台查看）</div>
                </el-form-item>

                <el-form-item label="" prop="">
                    <el-button type="primary" @click="copy()">
                        复制链接
                    </el-button>
                        <input v-model="link" ref="link" style="position:absolute;opacity:0;height:1px;" />
                    <div class="tip">点击复制小程序直播列表链接。</div>
                </el-form-item>
                <el-form-item label="" prop="">
                    <el-button type="success" @click="submit('form')">
                        提交
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
    <script>
        var vm = new Vue({
        el:"#app",
        delimiters: ['[[', ']]'],
            data() {
                let set = {!! $set ?: '{}' !!};
                let link = {!! $link ?: '{}' !!};
                return{
                    loading:false,
                    table_loading:false,
                    submit_loading:false,
                    link:link,
                    form:{
                        is_open : 0,
                        ...set
                    },
                    rules: {
                        appId: [
                            { required: true, message: '请输入appId', trigger: 'blur' },
                        ],
                        secret: [
                            { required: true, message: '请输入secret', trigger: 'blur' },
                        ],
                    },
                }
            },
            created(){

            },
            methods: {
                copy(row,index) {
                    that = this;
                    let Url = that.$refs['link'];
                    Url.select(); // 选择对象
                    document.execCommand("Copy",false);
                    that.$message({message:"复制成功！",type:"success"});
                },
                submit(formName) {
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            this.loading = true;
                            // delete(this.form['thumb_url']);
                            this.$http.post("{!! yzWebFullUrl('plugin.appletslive.admin.controllers.set.index') !!}",{'form_data':this.form}).then(response => {
                                if (response.data.result) {
                                    this.$message({type: 'success',message: '操作成功!'});
                                      window.location.href='{!! yzWebFullUrl('plugin.appletslive.admin.controllers.set.index') !!}';
                                    this.loading = false;
                                } else {
                                    this.$message({message: response.data.msg,type: 'error'});
                                    this.loading = false;
                                }
                            },response => {
                                this.loading = false;
                            });
                         }
                        else {
                            console.log('error submit!!');
                            return false;
                        }
                     });
                },
            },
        });
    </script>

@endsection
