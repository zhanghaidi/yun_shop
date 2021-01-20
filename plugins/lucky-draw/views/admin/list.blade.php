@extends('layouts.base')
@section('title', "活动列表")
@section('content')

<style>
    #re_content{
        margin-top:20px;
    }
    .el-form .el-form-item{
    margin-right: 60px;   
            }
    .list_total_num{
        margin-top:20px;
    }
    .el-table__header-wrapper{
        margin-top:20px;
    }
    .el-tag{font-weight:700;font-size:15px;}
    .rightlist-head{padding:15px 0;line-height:50px;}
    .rightlist-head-con{float:left;padding-right:20px;font-size:16px;color:#888;}

    .el-button+.el-button {
    margin-left: 0px;
    }
    .qr_code{
       text-align:center; 
    }
    </style>
<!-- tab -->

<div id='re_content' v-loading="all_loading">
    <div class="rightlist-head">
        <div class="rightlist-head-con">活动列表</div>
        <a href="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.add') }}">
            <el-button>添加活动</el-button>
        </a>
    </div>
    <template>
        <el-form :inline="true" :model="search_form" ref="search_form">
            <el-row>
                <el-col :span="24">
                    <el-form-item label="活动ID">
                        <el-input v-model="search_form.id" placeholder="请输入活动ID"></el-input>
                    </el-form-item>
                    <el-form-item label="活动名称">
                        <el-input v-model="search_form.name" placeholder="请输入活动名称"></el-input>
                    </el-form-item>
                    <el-form-item label="时间范围">
                        <el-date-picker v-model="search_form.times" type="datetimerange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
                    </el-form-item>
                    <el-form-item style="float:right;text-align:right;">
                        <a href="#">
                            <el-button type="success" icon="el-icon-search" @click="search()">搜索</el-button>
                        </a>
                        <a href="#">
                            <!-- <el-button type="default" @click="outExcel()">导出EXCEL</el-button> -->
                        </a>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        
        <template>
            <!-- 表格start -->
            <el-table :data="list" style="width: 100%" v-loading="search_loading">
                <el-table-column prop="id" label="ID" width="70px" align="center"></el-table-column>
                <el-table-column prop="name" label="活动名称" min-width="110" align="center"></el-table-column>
                <el-table-column prop="countdown_time" label="活动时间" min-width="100" align="center">
                    <template slot-scope="scope">
                        <div>[[scope.row.start_time]]</div>
                        <div>至</div>
                        <div>[[scope.row.end_time]]</div>
                    </template>
                </el-table-column>
                <el-table-column label="活动奖品" prop="prize_list"  align="center">
                    <template slot-scope="scope">
                        <el-popover
                            placement="right-start"
                            trigger="hover"
                            width="50"
                        >  
                        <div slot="reference"><div v-for="(item,index,key) in scope.row.prize_list.slice(0,3)" style="margin-left:5px;">[[item.name]]</div></div>
                        <div v-for="(item,index,key) in scope.row.prize_list"  >[[item.name]]</div>
                        </el-popover>
                    </template>
                </el-table-column>
                <el-table-column label="抽奖人数" prop="log_count" align="center"></el-table-column>
                <el-table-column label="中奖人数" prop="record_count" align="center"></el-table-column>
                <el-table-column label="操作" align="center" width="330px">
                    <template slot-scope="scope">
                        <a :href="'{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.record', array('id' => '')) }}'+[[scope.row.id]]">
                            <el-button size="mini">数据</el-button>
                        </a>
                        <el-popover
                            placement="top"
                            trigger="hover"
                            popper-class="qr_code"
                            >
                            <img :src="scope.row.qr_code" style="width:100px;height:100px;">
                            <el-button slot="reference" size="mini">二维码</el-button>
                        </el-popover>
                        <el-button size="mini" @click="copy(scope.$index)">链接</el-button>
                        <a :href="'{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.edit', array('id' => '')) }}'+[[scope.row.id]]"><el-button size="mini">编辑</el-button></a>
                        <el-button size="mini" @click="del(scope, list)">删除</el-button>
                        <el-input style="opacity:0;position: absolute;:1px;" v-model="scope.row.activity_url" :ref="'link'+scope.$index">
                    </template>
                </el-table-column>
            </el-table>
            <el-row>
                <el-col :span="24" align="right" migra style="padding:15px 5% 15px 0" v-loading="loading">
                    <el-pagination background layout="prev, pager, next" @current-change="currentChange" :total="page_total"
                        :page-size="page_size" :current-page="current_page"></el-pagination>
                </el-col>
            </el-row>
            <!-- 表格end -->
        </template>
            
    </template>

</div>

<script>
    var vm = new Vue({
        el: "#re_content",
        delimiters: ['[[', ']]'],
        data() {
            let data = {!! $page_list?:'{}' !!}
            let category_list = {!! $category_list?:'{}' !!}
            let activate_list = {!! $type_list?:'{}' !!};
            let lang = {!! $lang?:'{}' !!};

            let time_list = [
                {id:0,name:"不搜索时间"},
                {id:1,name:"搜索时间"},
            ];
            return {
                list:[],
                page_total:1,
                data:'',
                loading:false,
                page:1,
                page_size:1,
                activeName: 'activate',
                lang:lang,
                all_loading:false,
                loading:false,
                search_loading:false,
                search_form:{},
                real_search_form:"",
                activate_list:activate_list,
                category_list: category_list,
                // status_list:status_list,
                time_list:time_list,
                page_total:data.total,
                // list:data.data,
                data:data,
                page_size:data.per_page,
                current_page:data.current_page,
            }
        },
        created () {
            this.currentChange(1);
        },
        methods: {
            timestampToTime(timestamp) {
            var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
            var Y = date.getFullYear() + '-';
            var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
            var D = (date.getDate() < 10 ? '0'+date.getDate() : date.getDate()) + ' ';
            var h = (date.getHours() < 10 ? '0'+date.getHours() : date.getHours()) + ':';
            var m = (date.getMinutes() < 10 ? '0'+date.getMinutes() : date.getMinutes()) + ':';
            var s = (date.getSeconds() < 10 ? '0'+date.getSeconds() : date.getSeconds());
                return Y+M+D+h+m+s;
            },
            del(scope, rows){
               
                rows.splice(scope.$index, 1);
                let json={
                    id:scope.row.id
                }
                this.$http.post('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.del') !!}',json).then(function (response){
                    this.$message({message:"删除成功！",type:"success"});
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            getData(){
                this.$http.get('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.getList') !!}').then(function (response){
                    this.list=response.data.data.page_list.data;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            search() {
                this.search_loading = true;
                if(this.search_form.is_time != 0 && this.search_form.times){
                    this.search_form.start_time = Math.round(this.search_form.times[0]/1000).valueOf();
                    this.search_form.end_time = Math.round(this.search_form.times[1]/1000).valueOf();
                }else{
                    this.search_form.start_time = '';
                    this.search_form.end_time = '';
                }
                this.$http.post('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.search') !!}',{search:this.search_form}
                ).then(function (response) {
                    if (response.data.result){
                        let data = response.data.data.page_list;
                        this.page_total = data.total;
                        this.list = data.data;
                        this.list.map((item,index,key)=>{
                            item.start_time=this.timestampToTime(item.countdown_time[0]);
                            item.end_time=this.timestampToTime(item.countdown_time[1]);
                        });
                        this.page_size = data.per_page;
                        this.current_page = data.current_page;
                        this.loading = false;
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
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.getList') !!}',{page:val,search:this.search_form}).then(function (response){
                    let data = response.data.data.page_list;
                    this.page_total = data.total;
                    this.list = data.data;
                    this.list.map((item,index,key)=>{
                            item.start_time=this.timestampToTime(item.countdown_time[0]);
                            item.end_time=this.timestampToTime(item.countdown_time[1]);
                    })
                    this.page_size = data.per_page;
                    this.current_page = data.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            copy(index) {
                that = this;
                let Url = that.$refs['link'+index]; 
                Url.select(); // 选择对象
                document.execCommand("Copy",false);
                that.$message({message:"复制成功！",type:"success"});
            },
        },
    });
</script>
@endsection

