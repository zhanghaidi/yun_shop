@extends('layouts.base')
@section('title', "粉丝管理")
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
    
    .choose_group{background:#409EFF;color:white;}
    .el-checkbox.is-bordered + .el-checkbox.is-bordered {margin:0 0 3px 0;}
    .checkbox {text-align:left;}
    [v-cloak]{
        display:none;
    }
</style>

<div class="rightlist">
    <div id="app" v-loading="loading" v-cloak>
    <link rel="stylesheet" href="//at.alicdn.com/t/font_913727_gt395lrelsk.css">
   
        <div class="rightlist-head">
            <div class="rightlist-head-con">粉丝管理</div>
        </div>
        <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
            <el-tab-pane label="全部粉丝" name="all_setting">
                <el-row>
                    <el-col :span="17">
                        <el-select v-model="fans_type" placeholder="粉丝类型" style="width:120px;" clearable>
                            <!-- <el-option  label="全部粉丝" value="1"></el-option> -->
                            <el-option  label="已关注" value="1"></el-option>
                            <el-option  label="取消关注" value="0"></el-option>
                        </el-select>
                        <el-input style="width:40%;" v-model="keyword"></el-input>
                        <el-button icon="el-icon-search" @click="search"></el-button>
                    </el-col>
                    <el-col :span="6" align="right">
                        <el-button icon="el-icon-plus" type="primary" @click="openEditGroup(1)">添加分组</el-button>
                    </el-col>
                </el-row>
                <el-row style="padding-top:30px;" :gutter="20">
                    <!-- left -->
                    <el-col :span="17">
                        <div style="background:#f4f6f9;border:1px #e8e9eb solid;padding:15px 0 0 15px;">
                            <div style="padding-bottom:15px;">
                                <div style="float:left;padding-right:15px;">
                                    [[group_name]]
                                    <span v-show="choose_group!==0">
                                        <el-button size="mini" @click="openEditGroup(2)">重命名</el-button>
                                        <el-button size="mini" @click="delGroup(choose_group)" type="danger">删除</el-button>
                                    </span>
                                </div>
                                <!-- <div style="float:right;padding-right:15px;">
                                    <el-radio v-model.number="show_type" :label="1">全部</el-radio>
                                    <el-radio v-model.number="show_type" :label="2">会员</el-radio>
                                </div> -->
                            </div>
                            <div style="padding:20px 0;">
                                <el-checkbox v-model.number="is_all_choose" :true-label="1" :false-label="0" @change="chooseAll()">
                                    全选
                                </el-checkbox>
                                <el-button type="primary" size="mini" style="margin-left:20px" @click="openSetGroup()">打标签</el-button>
                                <el-button size="mini" @click="syncChoosed()">同步选中信息</el-button>
                                <el-button size="mini" @click="syncAll()">同步全部信息</el-button>
                                <el-button type="info" icon="el-icon-setting" size="mini" style="float:right;margin-right:20px;" @click="openSyncSetting">同步设置</el-button>
                            </div>
                        </div>
                        <div v-for="(item,index) in member_list" :key="index" style="border:1px #e8e9eb solid;padding:15px 0 15px 15px;float:left;width:100%;">
                            <div style="float:left;padding:10px 0;">
                                <el-checkbox v-model.number="item.is_choose" :true-label="1" :false-label="0" @change="chooseOne()"></el-checkbox>
                                <img :src="item.has_one_member[0].avatar_image" alt="" style="width:48px;height:48px;">
                            </div>
                            <div style="padding-left:15px;float:left;">
                                <div>
                                    <a :href="'{{ yzWebUrl('plugin.wechat.admin.staff.controller.staff.index', array('openid' => '')) }}'+[[item.openid]]">[[item.nickname]]</a>
                                </div>
                                <div>[[item.has_one_member[0].uid]]</div>
                                <div>
                                    <span v-if="item.new_group.length==0" style="padding:5px;margin-right:5px;background:#909399;color:white;border-radius:5px;">
                                        暂无分组
                                    </span>
                                    <span v-for="(item1,index1) in item.new_group" style="padding:5px;margin-right:5px;background:#409EFF;color:white;border-radius:5px;">
                                        [[item1]]
                                    </span>
                                    <span @click="openSetOneGroup(item)"><i class="el-icon-arrow-down"></i></span></div>
                            </div>
                            <div style="padding-right:15px;float:right;text-align:right;">
                                <div>
                                    <a :href="'{{ yzWebUrl('plugin.wechat.admin.staff.controller.staff.index', array('openid' => '')) }}'+[[item.openid]]">发送消息<i class="el-icon-arrow-right"></i></a>
                                </div>
                                <div style="line-height:32px;" v-if="item.follow==1">关注：[[item.followtime]]</div>
                                <div style="line-height:32px;" v-else>取消关注：[[item.unfollowtime]]</div>
                            </div>
                            
                        </div>
                        <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                            <el-pagination layout="prev, pager, next" @current-change="currentChange" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                        </el-col>
                    </el-col>
                    <!-- 分页 -->
                    
                    <!-- left -->
                    <!-- right -->
                    <el-col :span="6" style="border:1px #e8e9eb solid;">
                        <ul style="padding:20px;cursor:pointer">
                            <li @click="chooseGroup('item',0)" :class="{choose_group:choose_group==0}">全部分组&nbsp;([[data.fansTotal]])</li>
                            <li v-for="(item,index) in group_list" @click="chooseGroup(item,index)" :class="{choose_group:choose_group==item.id}" style="text-indent:25px">[[item.name]]&nbsp;([[item.count]])</li>
                            
                        </ul>
                    </el-col>
                    <!-- right -->
                </el-row>
            </el-tab-pane>
            <el-tab-pane label="粉丝同步设置" name="fans_setting">
                
                <el-card shadow="always" style="border:1px solid #b3d8ff;color:#333;background:#ecf5ff;line-height:28px;font-weight:600">
                    此同步设置是指系统后台自动同步系统数据中已关注粉丝的基本信息(不同步会员信息),如果开启,则每天凌晨3:00自动更新粉丝数据；<br>
                    若想同步粉丝及会员基本信息,请将 全部粉丝->同步设置 中的同步粉丝、会员信息选择后保存。

                </el-card>
                <div style="padding:30px;">
                    <div style="font-weight:900;padding-right:30px;display:inline-block;">是否开启粉丝同步</div>
                    <el-radio v-model.number="is_open_fans_sync" @change="syncFans" :label="1">开启</el-radio>
                    <el-radio v-model.number="is_open_fans_sync" @change="syncFans" :label="0">关闭</el-radio>
                </div>
            </el-tab-pane>
            
        </el-tabs>
        <el-dialog title="同步设置" :visible.sync="is_sync_setting" v-loading="dialog_loading">
            <div>
                <div style="display:inline-block;width:100px;">同步信息设置</div>
                <el-radio v-model.number="show_type" :label="0">仅同步粉丝信息</el-radio>
                <el-radio v-model.number="show_type" :label="1">同步粉丝、会员信息</el-radio>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="is_sync_setting = false">取 消</el-button>
                <el-button type="primary" @click="syncSetting">保 存</el-button>
            </span>
        </el-dialog>
        <el-dialog title="添加/编辑分组" :visible.sync="is_edit_group" v-loading="dialog_loading">
            <div>
                <div style="display:inline-block;">分组名字</div>
                <el-input v-model="add_group_name" style="width:70%;"></el-input>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="is_edit_group = false">取 消</el-button>
                <el-button type="primary" @click="addGroup">保 存</el-button>
            </span>
        </el-dialog>
        <!-- 多个打标签 -->
        <el-dialog title="打标签" :visible.sync="is_set_group" v-loading="dialog_loading">
            <div>
                <el-checkbox v-for="(item,index) in group_list" :key="index" v-model.number="item.is_choose"  :true-label="1" :false-label="0">[[item.name]]</el-checkbox>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="is_set_group = false">取 消</el-button>
                <el-button type="primary" @click="setGroup">保 存</el-button>
            </span>
        </el-dialog>
        <!-- 单个打标签 -->
        <el-dialog title="打标签" :visible.sync="is_set_one_group" v-loading="dialog_loading">
            <div>
                <el-checkbox v-for="(item,index) in one_group_list" :key="index" v-model.number="item.is_choose"  :true-label="1" :false-label="0">[[item.name]]</el-checkbox>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="is_set_one_group = false">取 消</el-button>
                <el-button type="primary" @click="setOneGroup">保 存</el-button>
            </span>
        </el-dialog>
        
    </div>
<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            return{
                activeName:"all_setting",
                show_type:0,
                real_show_type:0,
                is_all_choose:0,
                is_real_all_choose:0,
                is_sync_setting:false,
                is_edit:0,//添加分组还是编辑分组
                is_edit_group:false,//添加分组弹出框
                is_set_group:false,//多个打标签弹出框
                is_set_one_group:false,//单个打标签弹出框
                group_name:"全部",//分组名
                add_group_name:"",//添加/编辑时分组名
                choose_group:0,//选中的分组的id
                is_open_fans_sync:0,
                choose_list:{},
                data:{},
                member_list:[],
                group_list:[],
                one_group_list:[],//单个打标签时回显已有标签
                groups_id:[],//多个打标签选中的分组
                one_groups_id:[],//单个打标签选中的分组
                fans_id:[],//打标签选中的粉丝
                fan_info:{},//单个打标签选中的粉丝信息
                loading:false,
                dialog_loading:false,
                photolist:"",
                voicelist:"",
                videolist:"",
                fans_type:"",
                keyword:"",//搜索关键词
                 // 分页
                table_loading:false,
                total:0,
                per_size:0,
                current_page:0,
                rules:{},
            }
        },
        created () {
            this.getData('{}');
        },
        methods: {
            getData(json) {
                var that = this;
                that.loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.getFansList') !!}",json).then(response => {
                console.log(response);
                that.data = response.data;
                that.member_list = response.data.data;
                that.group_list = response.data.groups;
                that.total = response.data.total;
                that.per_size = response.data.per_page;
                that.current_page = response.data.current_page;
                that.show_type = response.data.syncMember;
                that.is_open_fans_sync = response.data.syncFans;
                console.log(that.total)
                let new_data_arr = [];
                let new_group_arr = [];
                let new_group = [];
                if(that.member_list){
                    that.member_list.forEach(item => {
                        item.followtime = that.timeStyle(item.followtime);//时间格式转换
                        if(item.unfollowtime!==0){
                            item.unfollowtime = that.timeStyle(item.unfollowtime);//时间格式转换
                        }
                        new_data_arr.push(Object.assign({},item,{is_choose:0,new_group:[]}))
                    });
                }
                if(that.member_list == null){
                    that.member_list=[];
                }
                if(that.group_list){
                    that.group_list.forEach(item => {
                        new_group_arr.push(Object.assign({},item,{is_choose:0}))
                    });
                }
                if(that.group_list == null){
                    that.group_list=[];
                }
                that.member_list = new_data_arr;
                that.group_list = new_group_arr;
                if(that.member_list){
                    that.member_list.forEach(item => {
                        item.groupid.forEach(item1 => {
                            for(let i=0;i<that.group_list.length;i++){
                                if(item1==that.group_list[i].id){
                                    item.new_group.push(that.group_list[i].name);
                                }
                            }
                        });
                    });
                }
                console.log(that.member_list)
                that.loading = false;
                }),function(res){
                    that.loading = false;
                };

            },
            add0(m) {
                return m<10?'0'+m:m
            },
            timeStyle(time) {
                let time1 = new Date(time*1000);
                let y = time1.getFullYear();
                let m = time1.getMonth()+1;
                let d = time1.getDate();
                let h = time1.getHours();
                let mm = time1.getMinutes();
                let s = time1.getSeconds();
                return y+'-'+this.add0(m)+'-'+this.add0(d)+' '+this.add0(h)+':'+this.add0(mm)+':'+this.add0(s);
            },
            // 全选
            chooseAll(){
                if(this.is_all_choose ==1) {
                    this.member_list.forEach(item =>{
                        item.is_choose = 1
                    });
                }
                else {
                    this.member_list.forEach(item =>{
                        item.is_choose = 0
                    });
                }
            },
            // 同步选中信息
            syncChoosed() {
                var that = this;
                that.loading = true;
                that.fans_id = [];//先归零
                that.member_list.forEach(item => {
                    if(item.is_choose == 1) {
                        that.fans_id.push(item.fanid)
                    }
                })
                if(that.is_all_choose ==1) {
                    that.fans_id = [];//先归零
                    that.member_list.forEach(item => {
                        that.fans_id.push(item.fanid);
                    })
                }
                if(that.fans_id.length==0){
                    that.$message.error("请选择粉丝");
                    that.loading = false;
                    return;
                }
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.syncBatch') !!}",{fansIds:that.fans_id}).then(response => {
                console.log(response);
                if(response.data.result==1) {
                    that.$message.success("操作成功！");
                    window.location.reload()
                }
                else {
                    that.$message.error(response.data.msg);
                    that.loading = false;

                }
                }),function(res){
                    that.loading = false;
                };
            },
            // 同步全部
            syncAll() {
                var that = this;
                that.loading = true;
                
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.syncAll') !!}",{}).then(response => {
                console.log(response);
                if(response.data.result==1) {
                    that.$message.success("操作成功！");
                    window.location.reload()
                }
                else {
                    that.$message.error(response.data.msg);
                    that.loading = false;
                }
                }),function(res){
                    that.loading = false;
                };
            },
            // 同步设置
            openSyncSetting(){
                this.is_sync_setting=true;
                this.real_show_type=this.show_type;
            },
            syncSetting() {
                var that = this;
                that.dialog_loading = true;
                if(that.real_show_type == that.show_type){
                    that.is_sync_setting = false;
                    that.dialog_loading = false;
                    return;
                }
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.syncSetting') !!}",{syncMember:that.show_type}).then(response => {
                console.log(response);
                if(response.data.result==1) {
                    that.$message.success("操作成功！");
                    that.is_sync_setting = false;
                    that.dialog_loading = false;
                }
                else {
                    that.$message.error(response.data.msg);
                    that.is_sync_setting = false;
                    that.dialog_loading = false;
                }
                }),function(res){
                    that.is_sync_setting = false;
                    that.dialog_loading = false;
                };

            },
            //粉丝同步设置
            syncFans() {
                var that = this;
                that.loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.fansSyncSetting') !!}",{syncFans:that.is_open_fans_sync}).then(response => {
                console.log(response);
                if(response.data.result==1) {
                    that.$message.success("操作成功！");
                    window.location.reload()
                }
                else {
                    that.$message.error(response.data.msg);
                    if(that.is_open_fans_sync == 0){
                        that.is_open_fans_syn=1;
                    }
                    else {
                        that.is_open_fans_syn=0;
                    }
                    that.loading = false;
                }
                }),function(res){
                    if(that.is_open_fans_sync == 0){
                        that.is_open_fans_syn=1;
                    }
                    else {
                        that.is_open_fans_syn=0;
                    }
                    that.loading = false;
                };
                
            },
            // 删除分组
            delGroup(id) {
                console.log(id);
                var that = this;
                that.$confirm('删除标签后，该标签下的所有用户将失去该标签属性。是否确定删除？', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                    that.loading = true;
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.deleteGroup') !!}",{id:id}).then(response => {
                    console.log(response);
                    if(response.data.result==1) {
                        that.$message.success("操作成功！");
                        window.location.reload();
                    }
                    else {
                        that.$message.error(response.data.msg);
                        that.loading = false;
                    }
                }),function(res){
                    console.log("请求接口出错");
                    that.loading = false;
                };
                    }).catch(() => {
                        that.$message.info("已取消删除")
                });
            },
            chooseOne() {
                var that = this;
                //单选时，判断是否选择了全部
                for(let i=0;i<that.member_list.length;i++) {
                    if(that.member_list[i].is_choose == 0){
                        that.is_real_all_choose = 0;
                        that.is_all_choose = 0;
                        break;
                    }
                    if(that.member_list[i].is_choose == 1){
                        that.is_real_all_choose = 1;
                    }
                    if(that.is_real_all_choose == 1){
                        that.is_all_choose = 1;
                    }
                }
            },
            // 多个打标签
            openSetGroup() {
                var that = this;
                that.group_list.forEach(item => {
                    item.is_choose = 0;
                })
                for(let i=0;i<that.member_list.length;i++) {
                    if(that.member_list[i].is_choose == 1){
                        that.is_set_group = true;
                        return;
                    }
                }
                that.$message.error("请选择粉丝")

            },
            setGroup() {
                var that = this;
                that.groups_id = [];//先归零
                that.fans_id = [];//先归零
                that.group_list.forEach(item => {
                    if(item.is_choose == 1){
                        that.groups_id.push(item.id);
                    }
                })
                that.member_list.forEach(item => {
                    if(item.is_choose == 1) {
                        that.fans_id.push(item.fanid)
                    }
                })
                console.log(that.groups_id);
                if(that.groups_id.length == 0) {
                    that.$message.error("请选择分组")
                }
                else {
                    that.dialog_loading = true;
                    if(that.is_all_choose ==1) {
                        that.fans_id = [];//先归零
                        that.member_list.forEach(item => {
                            that.fans_id.push(item.fanid);
                        })
                    }
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.batchSetFansGroups') !!}",{groupIds:that.groups_id,fansIds:that.fans_id}).then(response => {
                    console.log(response);
                    if(response.data.result==1) {
                        that.$message.success("操作成功！");
                        // window.location.reload()
                    }
                    else {
                        that.$message.error(response.data.msg);
                        that.is_set_group = false;
                        that.dialog_loading = false;

                    }
                    }),function(res){
                        that.is_set_group = false;
                        that.dialog_loading = false;
                    };
                }

            },
            // 单个打标签
            openSetOneGroup(item) {
                var that = this;
                that.is_set_one_group = true;
                that.fan_info = item;
                console.log(that.fan_info);
                // 回显状态先归零
                that.one_group_list.forEach(item1 => {
                    item1.is_choose = 0;
                });
                // 回显已选分组
                that.one_group_list = JSON.parse(JSON.stringify(that.group_list))
                console.log(that.one_group_list);
                that.one_group_list.forEach(item1 => {
                    for(let i=0;i<item.groupid.length;i++){
                        if(item1.id==item.groupid[i]){
                            item1.is_choose = 1;
                            continue;
                        }
                    }
                    
                });
            }, 
            setOneGroup() {
                var that = this;
                that.one_group_list.forEach(item => {
                    if(item.is_choose == 1){
                        that.one_groups_id.push(item.id);
                    }
                })
                // 判断选择后与选择前数据是否一致，先不做
                // if(that.one_groups_id.length == 0) {
                //     that.is_set_one_group = false;

                // }
                let fan = [];
                fan.push(that.fan_info.fanid);
                let json = {groupIds:that.one_groups_id,fansIds:fan};
                that.dialog_loading = true;
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.batchSetFansGroups') !!}",json).then(response => {
                console.log(response);
                if(response.data.result==1) {
                    that.$message.success("操作成功！");
                    // window.location.reload()
                }
                else {
                    that.$message.error(response.data.msg);
                    that.is_set_one_group = false;
                    that.dialog_loading = false;
                }
                }),function(res){
                    that.is_set_one_group = false;
                    that.dialog_loading = false;
                };

            },

           
            handleClick() {
                
            },
            openEditGroup(x) {
                var that = this;
                // 添加
                that.is_edit_group = true;
                if(x==1){
                    that.is_edit = 1;
                    that.add_group_name = "";
                }
                // 编辑
                if(x==2){
                    that.is_edit = 0;
                    that.add_group_name = that.group_name
                }
            },
            addGroup() {
                var that = this;
                that.dialog_loading = true;
                if(that.is_edit == 1) {
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.addGroup') !!}",{name:that.add_group_name}).then(response => {
                    console.log(response);
                    if(response.data.result==1) {
                        that.$message.success("添加成功！");
                        window.location.reload()
                    }
                    else {
                        that.$message.error(response.data.msg);
                        that.is_edit_group = false;
                        that.dialog_loading = false;
                    }
                    }),function(res){
                        that.is_edit_group = false;
                        that.dialog_loading = false;
                    };
                }
                else if(that.is_edit == 0) {
                    console.log(that.choose_group)
                    that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.fans.controller.fans.editGroup') !!}",{name:that.add_group_name,id:that.choose_group}).then(response => {
                    console.log(response);
                    if(response.data.result==1) {
                        that.$message.success("添加成功！");
                        window.location.reload()
                    }
                    else {
                        that.$message.error(response.data.msg);
                        that.is_edit_group = false;
                        that.dialog_loading = false;
                    }
                    }),function(res){
                        that.is_edit_group = false;
                        that.dialog_loading = false;
                    };
                }
            },
            // 选择分组
            chooseGroup(item,index) {
                var that = this;
                if(item.id){
                    that.choose_group = item.id;
                }
                else {
                    that.choose_group = 0;
                }
                let json = {};
                if(item.id!==0){
                    that.group_name = item.name;
                    that.choose_list = item;
                    json = {groupId:item.id};
                }
                if(item == 'item') {
                    that.group_name = "全部";
                    that.choose_list = {};
                    json = {};
                }
                that.getData(json);
            },
           
             // 分页
             currentChange(val){
                var that = this;
                let json = {page:val,groupId:that.choose_group,keyword:that.keyword,follow:that.fans_type};
                that.getData(json);
            },
            // 搜索
            search() {
                var that = this;
                let json = {page:1,groupId:that.choose_group,keyword:that.keyword,follow:that.fans_type};
                that.getData(json);
            }
        },
    })

</script>
@endsection
