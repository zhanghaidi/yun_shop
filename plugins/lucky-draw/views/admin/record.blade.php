@extends('layouts.base')
@section('title', "活动数据")
@section('content')

<style>
    #re_content{
        margin-top:20px;
    }
    .el-form .el-form-item{
        margin-right: 60px;   
    }
    .rightlist-head{padding:15px 0;line-height:50px;}
    .rightlist-head-con{padding-right:20px;font-size:16px;color:#888;display:inline-block;}
    #re_content .time{margin-bottom:20px;}
    </style>
<!-- tab -->

<div id='re_content' v-loading="all_loading">
    <div class="rightlist-head">
        <div class="rightlist-head-con">活动数据</div>
        <a href="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.index') }}">
            <el-button>返回列表</el-button>
        </a>
    </div>
    <div class="time">活动时间：[[start_time]]至[[end_time]]</div>
    <template>
        <el-form :inline="true" :model="search_form" ref="search_form">
            <el-row>
                <el-col :span="24">
                    <el-form-item label="会员名称">
                        <el-input v-model="search_form.name" placeholder="请输入会员名称"></el-input>
                    </el-form-item>
                    <el-form-item label="时间范围">
                        <el-date-picker v-model="search_form.times" type="datetimerange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
                    </el-form-item>
                    <el-form-item style="float:right;text-align:right;">
                        <a href="#">
                            <el-button type="primary" @click="derive" v-if="list.length>0">导出</el-button>
                        </a>
                        <a href="#">
                            <!-- <el-button type="default" @click="outExcel()">导出EXCEL</el-button> -->
                        </a>
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
        <!-- 表格start -->
        <el-table :data="list" style="width: 100%" >
            <el-table-column prop="member.uid" label="会员ID" align="center"></el-table-column>
            <el-table-column prop="member.nickname" label="会员" align="center">
                    <template slot-scope="scope">
                        <img :src="scope.row.member.avatar_image" style="width:30px;height:30px;padding:1px;border:1px solid #ccc;">
                        <div>[[scope.row.member.nickname]]</div>
                    </template>
            </el-table-column>
            <el-table-column prop="member.mobile" label="手机号" align="center"></el-table-column>
            <el-table-column prop="created_at" label="抽奖时间" align="center">
            </el-table-column>
            <el-table-column prop="has_one_prize.name" label="中奖信息" align="center">
            </el-table-column>
            <el-table-column prop="has_one_prize" label="奖励信息"  align="center">
                    <template slot-scope="scope" v-if="scope.row.has_one_prize">
                        <div v-if="scope.row.has_one_prize.type===1">
                            <span>[[scope.row.has_one_prize.has_one_coupon.name]]</span>
                            <span>优惠券</span>
                        </div>
                        <div v-if="scope.row.has_one_prize.type===2">
                            <span>[[scope.row.has_one_prize.point]]</span>
                            <span>积分</span>
                        </div>
                        <div v-if="scope.row.has_one_prize.type===3">
                            <span>[[scope.row.has_one_prize.love]]</span>
                            <span>[[love_name]]</span>
                        </div>
                        <div v-if="scope.row.has_one_prize.type===4">
                            <span>[[scope.row.has_one_prize.amount]]</span>
                            <span>余额</span> 
                        </div>
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
                let logs={!! $logs?:'{}' !!}
                let love_name={!! $love_name ?: '爱心值' !!}
                let activityModel={!! $activityModel?:'{}' !!}
                let activity_id=activityModel.id
                let start_time=this.timestampToTime(activityModel.countdown_time[0])
                let end_time=this.timestampToTime(activityModel.countdown_time[1])
            return {
                love_name : love_name,
                list:[
                    ...logs.data
                ],
                loading:false,
                activity_id:activity_id,
                page_total:logs.total,
                page_size:logs.per_page,
                current_page:logs.current_page,
                search_form:{},
                search_loading:false,
                all_loading:false,
                start_time:start_time,
                end_time:end_time,
                formData:{
                    name:null,
                    times:null,
                    start_time:null,
                    end_time:null
                },
            }
        },
        mounted () {
        
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
            currentChange(val) {
                this.loading = true;
                this.$http.post('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.recordPage') !!}',{page:val,id:this.activity_id,search:this.search_form}).then(function (response){
                    let datas = response.data.data.logs;
                    this.page_total = datas.total;
                    this.list = datas.data;
                    this.page_size = datas.per_page;
                    this.current_page = datas.current_page;
                    this.loading = false;
                },function (response) {
                    console.log(response);
                    this.loading = false;
                }
                );
            },
            search() {
                this.search_loading = true;
                console.log(this.search_form)
                if(this.search_form.is_time != 0 && this.search_form.times){
                    this.search_form.start_time = Math.round(this.search_form.times[0]/1000).valueOf();
                    this.search_form.end_time = Math.round(this.search_form.times[1]/1000).valueOf();
                }
                else{
                    this.search_form.start_time = '';
                    this.search_form.end_time = '';
                }
                this.formData = JSON.parse(JSON.stringify(this.search_form));
                let json={
                    search:this.search_form,
                    id:this.activity_id
                }
                this.$http.post('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.searchRecord') !!}',json
                ).then(function (response) {
                    if (response.data.result){
                        let data = response.data.data.logs;
                        console.log(data)
                        this.page_total = data.total;
                        this.list = data.data;
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
            derive(){
              if(!this.formData.name){
                this.formData.name=null;
              }
              if(!this.formData.times){
                 this.formData.times=null;
                 this.formData.start_time=null;
                 this.formData.end_time=null;
              }
              let host= '{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.export') !!}'+'&search[name]='+this.formData.name+'&search[times]='+this.formData.times+'&search[start_time]='+this.formData.start_time+'&search[end_time]='+this.formData.end_time+'&search[id]='+this.activity_id;
                window.location.href=host; 
            }
        },
    });
</script>
@endsection

