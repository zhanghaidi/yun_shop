@extends('layouts.base')
@section('title', "插件安装/升级")
@section('content')
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/vue-goods.css')}}"/>
    <style>
        .el-tag {margin-bottom: 0px;}
        .vue-page {width:calc(100% - 116px);}
        
    </style>
    <div id="qrcode" ref="qrcode" style="display:none;"></div>
    <div class="rightlist">
        <div id="app" v-cloak v-loading="all_loading">
            <template>
                <div class="second-list">
                    <div class="third-list">
                        <div class="form-list">
                           <div class="form-list-con" style="display:flex;margin:0 15px;padding-bottom:15px;">
                                <div style="flex:1">
                                    <div style="font-size:24px;font-weight:700;">插件安装/升级</div>
                                    <div style="color:red;padding:10px 0;font-weight:600">（更新插件后，请返回插件管理页面，将已更新了的插件禁用后再启用）</div>
                                </div>
                                <div style="flex:1;text-align:right">
                                    <el-input placeholder="请输入关键字" v-model="keyword" style="width:200px;"></el-input>
                                    <el-button type="primary" icon="el-icon-search" @click="search(1)">搜索</el-button>
                                    <el-button type="warning" icon="el-icon-back" @click="goto()">返回插件管理中心</el-button>
                                </div>
                           </div>
                        </div>
                        <div class="table-list">
                            <div style="margin-left:10px;">
                                <!-- <el-checkbox v-model.number="is_all_choose" :true-label="1" :false-label="0"
                                             @change="allChoose">[[is_all_choose==1?'全不选':'全选']]
                                </el-checkbox> -->
                                <el-radio-group v-model="status" @change="statusChange">
                                    <el-radio-button label="0">全部</el-radio-button>
                                    <el-radio-button label="1">未授权</el-radio-button>
                                    <el-radio-button label="2">未安装</el-radio-button>
                                    <el-radio-button label="3">已安装</el-radio-button>
                                </el-radio-group>
                                <el-button type="success" icon="el-icon-add" style="margin-left:50px;" @click="batchInstall" v-if="status==2 || status==0">批量安装</el-button>
                                <el-button type="success" icon="el-icon-add" style="margin-left:50px;" @click="batchUpdate" v-if="status==3">批量升级</el-button>
                            </div>
                            <div>
                                <template>
                                    <!-- 表格start -->
                                    <el-table :data="list" style="width: 100%"
                                        row-key="id"
                                        :expand-row-keys="expands"
                                        :class="table_loading==true?'loading-height':''"
                                        v-loading="table_loading"
                                        @selection-change="handleSelectionChange">
                                        <el-table-column
                                            type="selection"
                                            width="100"
                                            align="center"
                                            :selectable="checkSelectable"
                                        >
                                        </el-table-column>
                                        <el-table-column prop="down_time" label="名称" min-width="100" align="center"
                                                         class="edit-cell">
                                            <template slot-scope="scope">
                                                [[scope.row.title]]
                                            </template>
                                        </el-table-column>
                                        
                                        <el-table-column prop="version" label="版本" min-width="70" align="center"></el-table-column>
                                        <el-table-column prop="size" label="大小" min-width="70" align="center"></el-table-column>

                                        <el-table-column label="状态" prop="status_message" align="center">
                                            <template slot-scope="scope">
                                                <span v-if="scope.row.version_status == 'un_auth'">
                                                    <el-tag type="danger">未授权</el-tag>
                                                </span>
                                                <span v-if="scope.row.version_status == 'un_install'">
                                                    <el-tag type="warning">未安装</el-tag>
                                                </span>
                                                <span v-if="scope.row.version_status == 'installed'">
                                                    <el-tag>已安装</el-tag>
                                                </span>
                                                <span v-if="scope.row.version_status == 'new'">
                                                    <el-tag type="success">可升级</el-tag>
                                                </span>

                                            </template>
                                        </el-table-column>
                                        <el-table-column label="操作" width="300" align="center">
                                            <template slot-scope="scope">
                                                <div class="table-option">
                                                    <el-button style="width:100px" type="primary" slot="reference" @click="openDetail(scope.row.id)">基本信息</el-button>
                                                    <!-- 已安装状态且当前版本比新版本低 -->
                                                    <el-button style="width:100px" type="success" v-if="scope.row.version_status == 'new'" @click="installOne(scope.row,'2')">请升级</el-button>
                                                    <el-button style="width:100px" type="success" v-if="scope.row.version_status == 'installed'" :disabled="scope.row.version_status == 'installed'" @click="installOne(scope.row,'2')">已安装</el-button>
                                                    <el-button style="width:100px" type="success" v-else-if="scope.row.version_status == 'un_auth'" @click="installOne(scope.row,'3')" :disabled="true">安装</el-button>
                                                    <el-button style="width:100px" v-if="scope.row.version_status == 'un_install'" type="success" @click="installOne(scope.row,'1')">安装</el-button>
                                                </div>
                                            </template>
                                        </el-table-column>
                                        <el-table-column
                                            type="expand"
                                            align="right">
                                            <template slot-scope="scope">
                                                <div style="width:90%;margin:0 5%;border:1px solid #fafafa;border-radius:10px;background:#f5f7fa;padding:20px 10px;">
                                                    [[ scope.row.description ]]
                                                </div>
                                            </template>
                                        </el-table-column>
                                    </el-table>
                                    <!-- 表格end -->
                                </template>

                            </div>
                        </div>
                    </div>
                    <!-- 分页 -->
                    <div class="vue-page" v-show="total>1">
                        <el-row>
                            <el-col align="right">
                                <el-pagination layout="prev, pager, next,jumper" @current-change="search" :total="total"
                                               :page-size="per_size" :current-page="current_page" background
                                               v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </div>
                    <el-dialog title="授权" :visible.sync="dialog1">
                        <el-form ref="form" :model="impower" :rules="rules" label-width="15%">
                            <el-form-item label="密钥key" prop="key">
                                <el-input v-model="impower.key" placeholder="请输入密钥key" style="width:70%;"></el-input>
                            </el-form-item>
                            <el-form-item label="密钥secret" prop="secret">
                                <el-input v-model="impower.secret" placeholder="请输入密钥secret" style="width:70%;"></el-input>
                            </el-form-item>
                        </el-form>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="confirmImpower">授 权</el-button>
                            <el-button @click="dialog1=false">关 闭</el-button>
                        </span>
                    </el-dialog>

                </div>

            </template>

        </div>
    </div>
    <script src="{{resource_get('static/js/qrcode.min.js')}}"></script>
    <script>
        var app = new Vue({
            el: "#app",
            delimiters: ['[[', ']]'],
            data() {
                return {
                    list: [],//列表
                    expands:[],//展开行
                    all_loading: false,
                    impower: {
                        key:'',
                        secret:'',
                    },
                    row:{},
                    status:0,
                    keyword:'',
                    dialog1:false,

                    loading: false,
                    table_loading: false,
                    dialog_loading:false,
                    rules: {},
                    //分页
                    total: 0,
                    per_size: 0,
                    current_page: 0,
                    // last_page:0,
                    rules: {},
                }
            },
            created() {
                let json = {status:0};
                this.getData(json);
                let that = this;
                document.onkeydown = function(){
                    if(window.event.keyCode == 13)
                        that.search(1);
                }

            },
            mounted() {
            },
            methods: {
                getData(json) {
                    var that = this;
                    that.table_loading = true;
                    that.$http.post("{!! yzWebFullUrl('plugin.plugins-market.Controllers.new-market.getList') !!}",json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            // this.setData(response.data.data);
                            that.list = response.data.data.data;
                            that.total = response.data.data.total;
                            that.per_size = response.data.data.per_page;
                            that.current_page =  response.data.data.current_page;
                        } else {
                            that.$message.error(response.data.msg);
                        }
                        that.table_loading = false;
                    }), function (res) {
                        //console.log(res);
                        that.table_loading = false;
                    };
                },
                // 搜索、分页
                search(page) {
                    let that = this;
                    let json = {
                        status:that.status,
                        page:page,
                        keyword:that.keyword,
                    };
                    that.table_loading = true;
                    that.getData(json);
                    // that.$http.post("{!! yzWebFullUrl('goods.goods.goods-search') !!}", json).then(response => {
                    //     console.log(response);
                    //     if (response.data.result == 1) {
                    //         let arr = [];
                    //         that.goods_list = response.data.data.data;
                    //         that.goods_list.forEach((item, index) => {
                    //             item.title = that.escapeHTML(item.title)
                    //             arr.push(Object.assign({}, item, {is_choose: 0}))//是否选中
                    //         });
                    //         that.goods_list = arr;
                    //         that.total = response.data.data.total;
                    //         that.current_page = response.data.data.current_page;
                    //         that.per_size = response.data.data.per_page;
                    //     } else {
                    //         that.$message.error(response.data.msg);
                    //     }
                    //     that.table_loading = false;
                    // }), function (res) {
                    //     console.log(res);
                    //     that.table_loading = false;
                    // };
                },
                statusChange() {
                    let that = this;
                    let json = {
                        status:that.status,
                        page:1,
                        keyword:that.keyword,
                    };
                    that.table_loading = true;
                    that.getData(json);
                },
                // 安装、授权单个
                installOne(row,type) {
                    let that = this;
                    that.impower = {
                        key:'',
                        secret:'',
                    };
                    that.row = row;
                    if(type == 1 || type == 2) {
                        let json = {};
                        if(type == 1) {
                            json = {
                                plugin:{
                                    name:row.name,
                                    version:row.version,
                                }
                            }
                        }
                        else if(type == 2) {
                            json = {
                                plugin:{
                                    name:row.name,
                                    version:row.latestVersion,
                                }
                            }
                        }
                        that.table_loading = true;
                        that.$http.post("{!! yzWebFullUrl('plugin.plugins-market.Controllers.new-plugin.install') !!}",json).then(response => {
                            console.log(response);
                            if (response.data.result == 1) {
                                that.$message.success(response.data.msg);
                                // let json = {status:0}
                                // that.getData(json);
                                window.location.reload();
                            } else {
                                that.$message.error(response.data.msg);
                            }
                            that.table_loading = false;
                        }), function (res) {
                            //console.log(res);
                            that.table_loading = false;
                        };
                    }
                    else if(type == 3) {
                        that.openDialog(row);
                    }
                    console.log(row)
                },
                openDialog(row) {
                    let that = this;
                    that.dialog1 = true;

                },
                // 确认授权
                confirmImpower() {
                    let that = this;
                    console.log(that.impower);
                    if(that.impower.key == '') {
                        that.$message.error("密钥key不能为空");
                        return;
                    }
                    if(that.impower.secret == '') {
                        that.$message.error("密钥secret不能为空");
                        return;
                    }
                    let json = {
                        plugin:{
                            name:that.row.name,
                            version:that.row.version,
                        },
                        keyData:{
                            key:that.impower.key,
                            secret:that.impower.secret,
                        }
                    };
                    that.dialog_loading = true;
                    that.$http.post("{!! yzWebFullUrl('plugin.plugins-market.Controllers.new-plugin.authorize') !!}",json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            that.$message.success(response.data.msg);
                            // let json = {status:0}
                            // that.getData(json);
                            // that.dialog1 = false;
                            window.location.reload();
                        } else {
                            that.$message.error(response.data.msg);
                            // that.dialog1 = false;
                        }
                        that.dialog_loading = false;
                    }), function (res) {
                        //console.log(res);
                        that.dialog_loading = false;
                    };
                },
                batchInstall() {
                    let that = this;
                    let plugin = []
                    that.selected_list.forEach((item,index) => {
                        plugin.push({
                            name:item.name,
                            version:item.version
                        })
                    });
                    let json = {
                        plugin:plugin,
                    }
                    that.batch(json);
                },
                batchUpdate() {
                    let that = this;
                    let plugin = []
                    that.selected_list.forEach((item,index) => {
                        plugin.push({
                            name:item.name,
                            version:item.latestVersion
                        })
                    });
                    let json = {
                        plugin:plugin,
                    }
                    that.batch(json);
                    
                },
                batch(json) {
                    let that = this;
                    that.table_loading = true;
                    that.$http.post("{!! yzWebFullUrl('plugin.plugins-market.Controllers.new-plugin.batchInstall') !!}",json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            that.$message.success(response.data.msg);
                            // let json = {status:0}
                            // that.getData(json);
                            window.location.reload();

                        } else {
                            that.$message.error(response.data.msg);
                        }
                        that.table_loading = false;
                    }), function (res) {
                        //console.log(res);
                        that.table_loading = false;
                    };
                },
                checkSelectable(row) {
                    if(this.status == 0) {
                        if(row.version_status != "un_install") {
                            return false;
                        }
                        else {
                            return true;
                        }
                    }
                    else {
                        if(row.version_status != "new" && row.version_status != "un_install") {
                            return false;
                        }
                        else {
                            return true;
                        }
                    }
                    // if(row.version_status == "installed") {
                    //     return false;
                    // }
                    // else {
                    //     return true;
                    // }
                },
                handleSelectionChange(val) {
                    this.selected_list = val;
                    console.log(this.selected_list);
                },
                openDetail(id) {
                    let that = this;
                    if(that.expands[0] == id) {
                        console.log("wqqweqwe")
                        that.expands = [];
                        return false;
                    }
                    that.expands = [];
                    that.expands.push(id);
                },
                goto() {
                    // plugins.get-plugin-data
                    window.location.href='{!! yzWebFullUrl('plugins.get-plugin-data') !!}';
                },
            },
        })

    </script>
@endsection