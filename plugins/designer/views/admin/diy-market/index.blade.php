@extends('layouts.base')
@section('title', "DIY模板市场")
@section('content')
<style>
    .rightlist #app .rightlist-head{line-height:50px;padding:15px 0;}
    .rightlist #app{margin-left:30px;}
    .el-form-item__label{padding-right:30px;}
    .tip{font-size:12px;color:#999;}
    .rightlist-head-con{float:left;line-height:50px;padding-right:20px;font-size:16px;color:#888;}
    .el-tag{font-weight:700;font-size:15px;margin-bottom:30px;}
    .el-icon-edit{font-size:16px;padding:0 15px;color:#409EFF;cursor: pointer;}
    /* 滑块选择小白点 */
    .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
    .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
   
    input[type=file] {display: none;}
    [v-cloak]{display:none}
</style>

<div class="rightlist">
    <div id="app" v-loading.fullscreen.lock="dialog_loading" v-cloak>
    <link rel="stylesheet" href="//at.alicdn.com/t/font_432132_v610m1e8re.css">
        <div class="rightlist-head">
            <div class="rightlist-head-con">模板管理</div>
            <el-button type="primary" @click="sync">点击同步</el-button>
            <el-button v-if="page_total != 0" type="danger" @click="deleteAll">删除全部(危险)</el-button>
            <span v-if="page_total != 0" style="color:red">删除全部会清空本地数据(如果图片不显示,请尝试点击删除后重新同步)</span>
        </div>
        <div v-if="category.length != 0">
            <span style="font-weight:900">类型：</span>
            <el-radio-group v-model="category_name" @change="search()">
                <el-radio-button v-for="(item,index) in category" :key="index" :label="item" :value="item" >[[item]]</el-radio-button>
            </el-radio-group>
        </div>
        <div v-if="page.length != 0">
            <span style="font-weight:900">页面：</span>
            <el-radio-group v-model="page_id" @change="search()">
                <el-radio-button v-for="(item,index) in page" :key="index" :label="item" :value="item">[[item]]</el-radio-button>
            </el-radio-group>
        </div>
        <div  v-if="type.length != 0">
            <span style="font-weight:900">行业：</span>
            <el-radio-group v-model="type_id" @change="search()">
                <el-radio-button v-for="(item,index) in type" :key="index" :label="item" :value="item">[[item]]</el-radio-button>
            </el-radio-group>
        </div>
        
        <div style="float:left;width:100%" v-loading.fullscreen.lock="loading">
            <!-- 列表 -->
            <div v-for="(item,index) in list" style="margin-top:30px;">
                <div style="text-align:center;width:250px;margin:20px;float:left;">
                    <div style="margin:10px 0">[[item.title]]</div>
                    <div style="width:250px;height:450px;">
                        <img :src="item.thumb_url" style="width:250px;height:450px;" alt="">
                    </div>
                    <div style="margin:10px 0">
                        <div style="margin:10px 0;width:250px;float:left;position:relative;text-align:left;">
                            <span>【[[item.type]]】</span>
                            <span>【[[item.page]]】</span>
                            <el-button type="danger" size="mini" @click="choose(item.sync_id)" style="position:absolute;right:0;bottom:-3px;" :disabled="item.data==1">[[item.data==1?"已选取":"选取"]]</el-button>
                        </div>
                    </div>
                </div>
            </div>
            <el-col :span="24" align="right" migra style="padding:15px 5% 15px 0" v-if="page_total != 0">
                <el-pagination background layout="prev, pager, next" @current-change="currentChange" :total="page_total"
                    :page-size="page_size" :current-page="current_page"></el-pagination>
            </el-col>
            <el-col :span="24" align="center" migra style="padding:15px 5% 15px 0" v-if="page_total == 0">
                暂无数据！
            </el-col>
        </div>
    </div>
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            let type = {!! $type?:'{}' !!}
            console.log(type)
            let page_list = {!! $data?:'{}' !!}
            console.log(page_list)
            let page = {!! $page?:'{}' !!}
            console.log(page)
            let category = {!! $category?:'{}' !!}
            console.log(category)
            let arr = [];
            var a = 0;
            for(let i in category) {
                arr[a] = category[i];
                a++;
            }
            category = arr;

            let arr1 = [];
            var b = 0;
            for(let i in page) {
                arr1[b] = page[i];
                b++;
            }
            page = arr1;

            let arr2 = [];
            var c = 0;
            for(let i in type) {
                arr2[c] = type[i];
                c++;
            }
            type = arr2;
            console.log(type)
            return{
                dialog_loading:false,
                table_loading:false,
                loading:false,
                list:page_list.data,
                type_id:0,
                page_id:0,
                category_name:"",
                type:type,
                page_list:page_list,
                page:page,
                category:category,
                //分页
                page_total:page_list.total,
                current_page:page_list.current_page,
                page_size:page_list.per_page,

                rules:{},
            }
        },
        created() {
        },
        methods: {
           
            getList(json) {
                var that = this;
                that.loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.designer.admin.diy-market.search') !!}",json).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        that.page_total = response.data.data.total;
                        that.page_size = response.data.data.per_page;
                        that.current_page = response.data.data.current_page;
                        that.list = response.data.data.data;
                    }
                    else{
                        that.$message.error(response.data);
                    }
                    console.log(that.goods_list);
                    that.loading = false;
                }),function(res){
                    console.log(res);
                    that.loading = false;
                };
            },
            search() {
                var that = this;
                let json = {type_name:that.type_id,pages_name:that.page_id,category_name:that.category_name,page:1}
                that.getList(json);
            },
            currentChange(page) {
                var that = this;
                let json = {type_id:that.type_id,page_id:that.page_id,category_name:that.category_name,page:page}
                that.getList(json);
            },
            // 选取
            choose(id) {
                this.loading=true;
                this.$http.post('{!! yzWebFullUrl('plugin.designer.admin.diy-market.choose') !!}',{id:id}).then(function (response) {
                if (response.data.result) {
                    this.$message({type: 'success',message: response.data.msg});
                    window.location.href='{!! yzWebFullUrl('plugin.designer.admin.list.update') !!}'+'&id='+response.data.data.id;
                    // let json = {type_id:this.type_id,page_id:this.page_id,category_name:this.category_name,page:1}
                    // this.getList(json);
                }
                else{
                    this.$message({type: 'error',message: response.data.msg});
                    this.loading=false;
                }
            },function (response) {
                this.loading=false;
            }
            );
                
            },
            // 同步
            sync() {
                this.loading=true;
                this.$http.post('{!! yzWebFullUrl('plugin.designer.admin.diy-market.chick-sync') !!}').then(function (response) {
                if (response.data.result) {
                    this.$message({type: 'success',message: response.data.msg});
                    let json = {type_id:this.type_id,page_id:this.page_id,category_name:this.category_name,page:1}
                    this.getList(json);
                }
                else{
                    this.$message({type: 'error',message: response.data.msg});
                    this.loading=false;
                }
                },function (response) {
                    this.loading=false;
                }
                );
            },
            deleteAll() {
                this.$confirm('确定删除吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                    this.loading=true;
                    this.$http.post('{!! yzWebFullUrl('plugin.designer.admin.diy-market.deleteAll') !!}').then(function (response) {
                    if (response.data.result) {
                        this.$message({type: 'success',message: response.data.msg});
                        window.location.href='{!! yzWebFullUrl('plugin.designer.admin.diy-market.index') !!}';
                    }
                    else{
                        this.$message({type: 'error',message: response.data.msg});
                        this.loading=false;
                    }
                    },function (response) {
                        this.loading=false;
                    })
                }).catch(() => {
                    this.$message({type: 'info',message: '已取消删除'});
                    });
                
            },
            
        },
    })

</script>
@endsection
