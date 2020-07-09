@extends('layouts.base')
@section('title', "公众号设置")
@section('content')
<style>
    .rightlist #app .rightlist-head{line-height:50px;padding:15px 0;}
    .rightlist #app{margin-left:30px;}
    .rightlist-head-con{float:left;padding-right:20px;font-size:16px;color:#888;}
    .el-tag{font-weight:700;font-size:15px;margin-bottom:30px;}
    .el-icon-edit{font-size:16px;padding:0 15px;color:#409EFF;cursor: pointer;}
    /* 滑块选择小白点 */
    .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
    .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
    .member_list{}
</style>
<div class="rightlist">
<div id="app" v-loading="submit_loading">
        <div class="rightlist-head">
            <div class="rightlist-head-con">公众号设置</div>
            <div class="text-align:right">
            <a href="{{ yzWebUrl('plugin.wechat.admin.menu.controller.default-menu.index', array('type' => '1')) }}">
                <el-button type="primary" icon="el-icon-plus">添加新菜单</el-button></div>
            </a>
        </div>
        <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
            <el-tab-pane label="默认菜单" name="default">
                <el-tabs v-model="activeChild" @tab-click="handleClickChild">
                    <el-tab-pane label="默认菜单" name="menu">
                        默认菜单

                    </el-tab-pane>
                    <el-tab-pane label="历史菜单列表" name="list">
                        <template>
                            <el-table :data="data" style="width: 100%" v-loading="table_loading">
                                
                                <el-table-column prop="title" label="标题" align="center"></el-table-column>
                            
                                <el-table-column label="显示对象" min-width="150" align="center">
                                <template slot-scope="scope">
                                    所有粉丝
                                </template>
                                </el-table-column>
                                <el-table-column label="是否在微信生效(只能生效一个默认菜单)" :render-header="renderHeader" align="center">
                                    <template slot-scope="scope">
                                        <el-button  type="primary" size="small" @click="showChange(scope.row,scope.row.id,scope.$index)" title="点击生效">点击生效</el-button>
                                    </template>
                                </el-table-column>
                                <el-table-column label="操作" align="center">
                                    <template slot-scope="scope">
                                        <a v-bind:href="'{{ yzWebUrl('plugin.wechat.admin.menu.controller.default-menu.index', array('id' => '')) }}'+[[scope.row.id]]">
                                            <el-button>
                                                编辑
                                            </el-button>
                                        </a>
                                        <el-button type="danger" @click="delRow(scope.$index,scope.row.id)">
                                            删除
                                        </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <el-row>
                                <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                    <el-pagination layout="prev, pager, next" @current-change="currentChange" :total="page_total" :page-size="page_size" background v-loading="loading"></el-pagination>
                                </el-col>
                            </el-row>
                        </template>
                    </el-tab-pane>
                </el-tabs>
            </el-tab-pane>
            <el-tab-pane label="个性化菜单" name="individuation">
                个性化菜单
            </el-tab-pane>
        </el-tabs>

    </div>
</div>
<script>
    var app = new Vue({
        el:"#app",
        delimiters: ['[[', ']]'],
            data(){
                let data ={!! $data?:'{}' !!};
                console.log(data);
                return{
                    activeName:"default",
                    activeChild:"list",
                    submit_loading:false,
                    table_loading:false,
                    loading:false,
                    page_total:"",
                    page_size:"",
                    data:data.data,
                    page_total:data.total,
                    page_size:data.per_page,
                    current_page:data.current_page,
                }
            },
        methods:{
            handleClick(tab, event) {
                this.submit_loading = true;
                if(tab.name=="default"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.index') !!}';
                }
                if(tab.name=="individuation"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.conditional-menu.conditional-menu') !!}';
                }
                console.log(event);
            },
            handleClickChild(tab, event){
                this.submit_loading = true;
                if(tab.name=="menu"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.index') !!}';
                }
                if(tab.name=="list"){
                    window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.display-menu') !!}';
                }
            },
            renderHeader(h) {
                return [h('p', {}, ['是否在微信生效']), h('p', {}, ['(只能生效一个默认菜单)'])]
            },
            delRow(index,id){
                this.$confirm('确定删除吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                    this.table_loading=true;
                    this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.del-menu') !!}',{id:id}).then(function (response) {
                    if (response.data.result) {
                        this.data.splice(index,1);
                        this.$message({type: 'success',message: '删除成功!'});
                    }
                    else{
                        this.$message({type: 'error',message: '删除失败!'});
                    }
                    this.table_loading=false;
                },function (response) {
                    this.table_loading=false;
                }
                );
                }).catch(() => {
                    this.$message({type: 'info',message: '已取消删除'});
                    });
            },
            showChange(row,id,index){
                this.submit_loading=true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.enable-menu') !!}',{id:id}
                ).then(function (response) {
                    console.log(response)
                    if (response.data.result){
                        this.$message.success({message:"保存成功！"});
                        window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.display-menu') !!}';
                    }
                    else {
                        this.$message({message: response.data.msg,type: 'error'});
                    }
                    this.submit_loading = false;
                    },function (response) {
                    this.submit_loading = false;
                    this.$message({message: response.data.msg,type: 'error'});
                }
                );

            },
            search() {
                console.log(this.search_form);
                this.search_loading=true;
                this.$http.post('{!! yzWebFullUrl('plugin.asset.Backend.Modules.member.Controllers.records.search') !!}',{search:this.search_form}
                ).then(function (response) {
                    console.log(response)
                    if (response.data.result){
                        // console.log(this.response.data)
                        let datas = response.data.data;
                        this.page_total = datas.total;
                        this.list = datas.data;
                        this.page_size = datas.per_page;
                        this.current_page = datas.current_page;
                        this.real_search_form = Object.assign({},this.search_form);
                    }
                    else {
                        this.$message({message: response.data.msg,type: 'error'});
                    }
                    this.search_loading = false;
                    },function (response) {
                    this.search_loading = false;
                    this.$message({message: response.data.msg,type: 'error'});
                }
                );
            },
                currentChange(val) {
                console.log(val)
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.menu.controller.default-menu.display-menu') !!}', {page: val}).then(function (response) {
                    console.log(response)
                    let datas = response.data;
                        this.page_total = datas.total;
                        this.data = datas.data;
                        this.page_size = datas.per_page;
                        this.current_page = datas.current_page;
                        this.loading = false;
                    },
                    function (response) {
                        console.log(response);
                        this.$message({
                            message: response.data.msg,
                            type: 'error'
                        });
                        this.loading = false;
                    }
                );
            }
        }
    }) 

</script>
@endsection
