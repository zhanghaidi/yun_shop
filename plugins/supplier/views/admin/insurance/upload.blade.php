@extends('layouts.base')
@section('title', "上传保单")
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

        .pdf-list{display:flex;padding-top:15px;width:83%;flex-wrap: wrap;}
        .pdf-list-all{flex:0 0 150px;height:50px;border:1px solid #ccc;border-radius: 5px;overflow: hidden;line-height: 24px;word-break: break-all;font-size: 13px;padding: 2px 5px;margin:5px;position: relative;}
        .pdf-list-del{position: absolute;top: -5px;right: 1px;cursor: pointer;}
    </style>

    <div class="rightlist">
        <div id="app" v-loading="submit_loading">
            <el-form ref="form" :model="form" :rules="rules" ref="form" label-width="240px">
                <h5 class="rightlist-head">
                    上传保单
                </h5>

                <el-form-item label="选择供应商" prop="">
                    <el-input v-model="form.word" disabled style="width:70%"></el-input>
                    <el-button @click="is_show=true">选择</el-button>
                    <div class="pdf-list">
                        <div class="pdf-list-all" v-for="(item,index) in choose_member" :key="index" style="display:flex">
                            <div style="padding-right:10px;flex:0 0 50px">
                                <img  :src="item.avatar" alt="" style="width:40px;height:40px">
                            </div>
                            <div style="padding-right:10px;display:inline-block;line-height:25px;overflow:hidden;flex:1">
                                <div>ID:[[item.id]]</div>
                                <div>用户名:[[item.username]]</div>
                            </div>
                            <div class="pdf-list-del" @click="delMember(item,index)">
                                <i class="fa fa-times"></i>
                            </div>
                        </div>
                    </div>
                </el-form-item>

                <el-form-item label="上传文件" prop="">
                    <el-input v-model="form.file" style="width:65%" placeholder="请上传PDF格式文件" disabled></el-input>
                        <el-upload class="el-button el-button-primary" action="{{ yzWebFullUrl('plugin.supplier.admin.controllers.insurance.insurance.uploadPdf') }}" multiple accept=".PDF" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
                            点击上传
                        </el-upload>
                        <div class="pdf-list">
                            <div class="pdf-list-all" v-for="(item,index) in pdf_list" :key="index">
                                <div style="padding-right:10px">[[item.file_name]]</div>
                                <div class="pdf-list-del" @click="del(item,index)">
                                    <i class="fa fa-times"></i>
                                </div>
                            </div>
                        </div>
                </el-form-item>

                <el-form-item label="" prop="">
                    <el-button type="success" @click="submit('form')">
                        提交
                    </el-button>
                </el-form-item>
            </el-form>
            <!-- 新增活动弹出框 -->
        <el-dialog title="选择供应商" :visible.sync="is_show" width="60%" v-loading="dialog_loading">
            <div>
                <el-input v-model="member_name" placeholder="请输入关键字" style="width:60%"></el-input>
                <a href="#">
                    <el-button type="success" icon="el-icon-search" @click="searchMembers()">搜索</el-button>
                </a>
                <template>
                    <!-- 表格start -->
                    <el-table :data="members" style="width: 100%;height:600px;overflow-y:auto" max-height="550" v-loading="table_loading">
                        <el-table-column prop="id" label="会员ID" align="center"></el-table-column>

                        <el-table-column prop="id" label="" width="90" align="center">
                            <template slot-scope="scope">
                                <img :src="scope.row.avatar" alt="" style="width:50px;height:50px;">
                            </template> 
                        </el-table-column>
                        <el-table-column prop="username" label="用户名"  min-width="120"></el-table-column>
                        <el-table-column label="操作" min-width="80" align="center">
                            <template slot-scope="scope">                     
                                <el-button type="primary" @click="choose(scope.row,scope.$index)">选择</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <!-- 表格end -->
                </template> 
                    <!-- 分页 -->
                <el-row>
                    <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                        <el-pagination layout="prev, pager, next" @current-change="currentChange" :current-page="current_page" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                    </el-col>
                </el-row>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="new_url = false">取 消</el-button>
                <!-- <el-button type="primary" @click="img_text_url = false">确 定</el-button> -->
            </span>
        </el-dialog>
        </div>
    </div>
    <script>
        var vm = new Vue({
            el:"#app",
            delimiters: ['[[', ']]'],
            data() {
                let set = {!! $set ?: '{}' !!};
                return{
                    loading:false,
                    dialog_loading:false,
                    table_loading:false,
                    submit_loading:false,
                    is_show:false,
                    members: [],
                    pdf_list:[],
                    choose_member:[],
                    member_name:'',
                    fileList:[],
                    value: [],
                    list: [],
                    status:[],
                    form:{
                        title : '',
                        ...set
                    },

                     //会员分页
                    total:0,
                    per_size:0,
                    current_page:0,
                    rules:{
                    },
                }
            },
            created(){

            },
            methods: {
                handleRemove(file, fileList) {
                    console.log(file, fileList);
                },
                handlePreview(file) {
                    console.log(file);
                },
                handleExceed(files, fileList) {
                    this.$message.warning(`当前限制选择 3 个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
                },
                beforeRemove(file, fileList) {
                    return this.$confirm(`确定移除 ${ file.name }？`);
                },
                uploadSuccess(res, file) {
                    if (res.result == 1) {
                        // pdf_list要回传的数据
                        this.pdf_list.push(res.data);
                        console.log(this.pdf_list)
                        this.$message.success("上传成功！");
                    } else {
                        this.$message.error(res.msg);
                    }
                    this.submit_loading = false;
                },
                beforeUpload(file) {
                    this.submit_loading = true;
                },
                loadMembers(query) {
                    if (query !== '') {
                        this.loading = true;
                        this.$http.post("{!! yzWebUrl('#') !!}",{keyword:query,asset_id:this.asset_id}).then(response => {
                            this.members = response.data.data.memberList;
                            // this.data=response.data.data;
                            this.loading = false;
                        }, response => {
                            this.$message({type: 'error',message: response.data.msg});
                            console.log(response);
                        });
                    } else {
                        this.members = [];
                    }
                },
                // 分页
                currentChange() {
                    
                },
                // 选择供应商
                choose(row,index) {
                    // 是否重复选择
                    let find1 = this.choose_member.find((item,index) => {
                        return item.id == row.id
                    })
                    console.log(find1)
                    if(find1) {
                        this.$message.error('该供应商已选择');
                        return false;
                    }
                    let json = {};
                    json = row;
                    json.word = "【ID:"+row.id+"】【用户名："+row.username+"】"
                    this.choose_member.push(json);
                    console.log(this.choose_member)
                },
                searchMembers() {
                    var that = this;
                    that.table_loading = true;
                    that.$http.post("{!! yzWebFullUrl('plugin.supplier.admin.controllers.insurance.insurance.searchSupplierByName') !!}",{keyword:that.member_name}).then(response => {
                        console.log(response);
                        if(response.data.result==1){
                            that.members = response.data.data.supplier;
                        }
                        else{
                            that.$message.error(response.data);
                        }
                        console.log(that.goods_list);
                        that.table_loading = false;
                    }),function(res){
                        console.log(res);
                        that.table_loading = false;
                    };
                },
                submit(formName) {
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            if(this.choose_member.length<=0){
                                this.$message.error('请选择供应商');
                                return false;
                            }
                            if(this.pdf_list.length<=0){
                                this.$message.error('请上传文件');
                                return false;
                            }
                            let list = [];
                            this.pdf_list.forEach((item,index) =>{
                                list.push(item.url)
                            });

                            let list_id = [];
                            this.choose_member.forEach((item,index) =>{
                                list_id.push(item.id)
                            });
                            
                            let json = {
                                file:list,
                                supplier_id:list_id
                            };
                            
                            this.submit_loading = true;
                            this.$http.post("{!! yzWebFullUrl('plugin.supplier.admin.controllers.insurance.insurance.upload') !!}",{'form':json}).then(response => {
                                if (response.data.result) {
                                    this.$message({type: 'success',message: '操作成功!'});
                                    window.location.href='{!! yzWebFullUrl('plugin.supplier.admin.controllers.insurance.insurance.index') !!}';
                                    this.submit_loading = false;
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
                del(item,index) {
                    this.pdf_list.splice(index,1);
                },
                delMember(item,index) {
                    this.choose_member.splice(index,1);
                },
            },
        });
    </script>

@endsection
