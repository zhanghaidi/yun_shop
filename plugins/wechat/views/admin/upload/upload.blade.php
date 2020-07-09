@extends('layouts.base')
@section('title', "上传文件")
@section('content')
<style>
    .rightlist #app .rightlist-head{line-height:50px;padding:15px 0;}
    .rightlist #app{margin-left:30px;}
    .el-form-item__label{padding-right:30px;}
    .tip{font-size:12px;color:#999;}
    /* .rightlist-head-con{padding-right:20px;font-size:16px;color:#888;} */
    .rightlist-head-con{padding-right:20px;font-size:16px;color:#888;}
    .el-tag{font-weight:700;font-size:15px;margin-bottom:30px;}
    .el-icon-edit{font-size:16px;padding:0 15px;color:#409EFF;cursor: pointer;}
    /* 滑块选择小白点 */
    .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
    .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
   
    input[type=file] {display: none;}
</style>

<div class="rightlist">
    <div id="app" v-loading="dialog_loading">
    <link rel="stylesheet" href="//at.alicdn.com/t/font_432132_v610m1e8re.css">
        <div class="rightlist-head">
            <div class="rightlist-head-con">上传JS文件</div>
        </div>
        <div>
        <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.upload.uploadjs.index') !!}" accept=".txt" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
            <el-button type="primary">上传JS文件</el-button>
        </el-upload> 
        </div>
    </div>
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            
            return{
                dialog_loading:false,
                rules:{},
            }
        },
        methods: {
            // 上传图片之前
            beforeUpload(){
                this.dialog_loading=true;
            },
            // 上传图片成功之后
            uploadSuccess(response,file,fileList){
                console.log(response);
                if(response.result==1){
                    this.$message.success("上传成功！ 地址为" + JSON.stringify(response.data));
                }
                else{
                    this.$message.error(response.msg);
                }
                this.dialog_loading=false;
            },
            
        },
    })

</script>
@endsection
