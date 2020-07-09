@extends('layouts.base')

@section('content')
@section('title', trans('商品列表'))
<style>
    [v-cloak]{
        display:none;
    }

</style>

<div class="w1200 ">

    <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
    <div id="goods-index" class=" rightlist ">
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">商品列表</a></li>
            </ul>
        </div>
        <div class="right-addbox">
            <div id="app" >
                <div class="panel panel-info">
                    <div class="panel-body">
                        <el-form :inline="true" :model="search_form" ref="search_form">
                            <el-form-item label="筛选条件">
                                <el-select v-model="search_form.commission_agent" clearable  placeholder="请选择商品库" clearable>
                                    <el-option  v-for="(item,index) in goods_agent"
                                                :key="index"
                                                :label="item.label"
                                                :value="item.value"></el-option>
                                </el-select>
                                <el-select v-model="search_form.source" clearable  placeholder="请选择来源" clearable @change="changeSource()">
                                    <el-option  v-for="(item,index) in goods_source"
                                                :key="index"
                                                :label="item.label"
                                                :value="item.value"></el-option>
                                </el-select>
                                <el-select v-model="search_form.shipping" clearable  placeholder="是否包邮" clearable >
                                    <el-option  v-for="(item,index) in goods_shipping"
                                                :key="index"
                                                :label="item.label"
                                                :value="item.value"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="">
                                <el-select v-model="search_form.cate_v1" placeholder="请选择一级分类" clearable @change="searchCateV2()">
                                    <el-option v-for="item in search_cate_v1" :key="item.id" :label="item.title" :value="item.id"></el-option>
                                </el-select>
                                <el-select v-model="search_form.cate_v2" placeholder="请选择二级分类" clearable @change="searchCateV3()">
                                    <el-option v-for="item in search_cate_v2" :key="item.id" :label="item.title" :value="item.id"></el-option>
                                </el-select>
                                <el-select v-model="search_form.cate_v3" placeholder="请选择三级分类" clearable>
                                    <el-option v-for="item in search_cate_v3" :key="item.id" :label="item.title" :value="item.id"></el-option>
                                </el-select>
                            </el-form-item>
                            <br>
                            <el-form-item label = "　　　　">
                                <el-select v-model="search_form.range_type" clearable  placeholder="请选择区间筛选类型">
                                    <el-option label="协议价格" value="agreement_price"></el-option>
                                    <el-option label="常规利润率" value="promotion_rate"></el-option>
                                    <el-option label="营销价格" value="activity_price"></el-option>
                                    <el-option label="指导价格" value="guide_price"></el-option>
                                    <el-option label="营销利润率" value="activity_rate"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="">
                                <el-col :span="11">
                                    <el-input  type="number" placeholder="区间开始" v-model="search_form.range_from"></el-input>
                                </el-col>
                                <el-col class="line" :span="2" style="border:0;font-size:25px;">-</el-col>
                                <el-col :span="11">
                                  <el-input type="number" placeholder="区间结束" v-model="search_form.range_to" ></el-input>
                                </el-col>
                            </el-form-item>
                            <el-form-item label="">
                                <el-col>
                                    <el-input  type="text" placeholder="商品名称、品牌" v-model="search_form.word"></el-input>
                                </el-col>
                            </el-form-item>
                            <el-form-item label="">
                                <el-select v-model="search_form.goods_page_size" clearable  placeholder="每页条数" clearable >
                                    <el-option  v-for="(item,index) in goods_page_size"
                                                :key="index"
                                                :label="item.label"
                                                :value="item.value"></el-option>
                                </el-select>
                                <el-select v-model="search_form.goods_import" clearable  placeholder="是否已导入" clearable >
                                    <el-option  v-for="(item,index) in goods_import"
                                                :key="index"
                                                :label="item.label"
                                                :value="item.value"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="success" icon="el-icon-search" @click="search()">搜索</el-button>
                            </el-form-item>
                        </el-form>
                        <el-form :model="category_form" ref="category_form" :inline="true">
                            <el-form-item label="选择导入分类">
                                <el-select v-model="category_form.id_v1" placeholder="请选择一级分类" clearable @change="changeV1()">
                                    <el-option v-for="item in category_list" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                </el-select>
                                <el-select v-model="category_form.id_v2" placeholder="请选择二级分类" clearable @change="changeV2()">
                                    <el-option v-for="item in category_list_v2" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                </el-select>
                                <el-select v-model="category_form.id_v3" placeholder="请选择三级分类" clearable v-if="category_level == 3">
                                    <el-option v-for="item in category_list_v3" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                </el-select>
                            </el-form-item>

                            <el-form-item label="选择导入商品标签">
                                <el-select v-model="category_form.fid_v1" placeholder="请选择标签组" clearable @change="filteringV1()">
                                    <el-option v-for="item in filtering_list" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                </el-select>
                                <el-select v-model="category_form.fid_v2" placeholder="请选择标签值" clearable >
                                    <el-option v-for="item in filtering_list_v2" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                </el-select>
                            </el-form-item>

                        </el-form>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-body table-responsive">
                        <div  v-cloak  v-loading="loading">
                            <!-- 表格start -->
                            {{--<el-table ref="multipleTable" :data="tableData" tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">--}}
                            <el-table :data="list" style="width: 100%" max-height="600" v-loading="all_loading" tooltip-effect="dark"
                                      @selection-change="handleSelectionChange">>
                                <el-table-column type="selection" prop="id" width="55"></el-table-column>
                                <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
                                <el-table-column label="商品" max-width="100">
                                    <template slot-scope="scope">
                                        <img :title="scope.row.title" :src="scope.row.cover" style="width:40px;height:40px;">
                                    </template>
                                </el-table-column>
                                <el-table-column prop="title" label="商品名称" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="promotion_rate" label="利润率" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="agreement_price" label="协议价格" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="guide_price" label="指导价格" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="market_price" label="市场价格" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="activity_price" label="营销价" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="activity_rate" label="营销利润率" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="third_brand_name" label="推荐品牌" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="third_category_name" label="推荐分类" max-width="100" align="center"></el-table-column>
                                <el-table-column prop="stock" label="库存"  max-width="100" align="center"></el-table-column>
                                <el-table-column label="状态"  max-width="100" align="center">
                                    <template slot-scope="scope">
                                        <p v-if="scope.row.status == 1">正常</p>
                                        <p v-else >下架</p>
                                        <p v-if="scope.row.is_presence == 1"><small style="color:#FF0000;">已导入</small></p>
                                        {{--<p v-else><small style="color:#409EFF;">未导入</small></p>--}}
                                    </template>
                                </el-table-column>
                            </el-table>
                            <div class="vue-page" v-show="total>1">
                                <el-row>
                                    <el-col align="right">
                                        <el-pagination layout="prev, pager, next,jumper" @current-change="search" :total="total"
                                                       :page-size="per_size" :current-page="current_page" background
                                                       v-loading="loading"></el-pagination>
                                    </el-col>
                                </el-row>
                            </div>


                            
                            <!-- 表格end -->
                        </div>
                    </div>
                    <div class='panel-footer'>
                        <input name="submit" type="button" @click="confirm()" :disabled="is_import_disabled" class="btn btn-success" value="导入商品">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    var app = new Vue({
        el:"#app",
        delimiters: ['[[', ']]'],
        directives: {
            'el-select-loadmore': {
                bind(el, binding) {
                    // 获取element-ui定义好的scroll盒子
                    const SELECTWRAP_DOM = el.querySelector('.el-select-dropdown .el-select-dropdown__wrap');
                    SELECTWRAP_DOM.addEventListener('scroll', function () {
                        /**
                         * scrollHeight 获取元素内容高度(只读)
                         * scrollTop 获取或者设置元素的偏移值,常用于, 计算滚动条的位置, 当一个元素的容器没有产生垂直方向的滚动条, 那它的scrollTop的值默认为0.
                         * clientHeight 读取元素的可见高度(只读)
                         * 如果元素滚动到底, 下面等式返回true, 没有则返回false:
                         * ele.scrollHeight - ele.scrollTop === ele.clientHeight;
                         */
                        const condition = this.scrollHeight - this.scrollTop <= this.clientHeight;
                        if (condition) {
                            binding.value();
                        }
                    });
                }
            }
        },
        data() {
            let category_level = {!! $category_level !!};
            let filtering_list = {!! $filtering_list?:'{}' !!};
            let category_list = {!! $category_list?:'{}' !!};
            let search_cate_v1 = {!! $search_cate_v1?:'{}' !!};

            return{
                loading:false,
                all_loading:false,
                page_number: 20, //每页显示条数

                list: [],
                search_form: {
                    cate_v1:'',
                    cate_v2:'',
                    cate_v3:'',
                    range_type:'',
                    range_from:'',
                    range_to:'',
                    source:'京东',
                    commission_agent:'1',
                    goods_import:''
                },
                category_page: {
                    pageIndex: 1,
                    pageSize: 20,
                },
                category_form:{
                    id_v1:"",
                    id_v2:"",
                    id_v3:"",
                    fid_v1:"",
                    fid_v2:"",
                },
                goods_source:[{
                    value: '2',
                    label: '京东',

                }, {
                    value: '6',
                    label: '阿里'
                }, {
                    value: '7',
                    label: '天猫'
                }],
                goods_shipping:[{
                    value: '',
                    label: '',
                }, {
                    value: '0',
                    label: '不包邮'
                }, {
                    value: '1',
                    label: '包邮'
                }],
                goods_page_size:[{
                    value: '20',
                    label: '20条'
                }, {
                    value: '50',
                    label: '50条'
                }, {
                    value: '100',
                    label: '100条'
                }],
                goods_import:[{
                    value: '',
                    label: ''
                }, {
                    value: '1',
                    label: '未导入'
                }, {
                    value: '2',
                    label: '已导入'
                }],
                goods_agent:[{
                    value: '0',
                    label: '选品库商品',

                }, {
                    value: '1',
                    label: '全部商品'
                }],

                is_import_disabled : false,

                filtering_list:filtering_list, //商品标签
                filtering_list_v2:[], //商品标签
                category_level:category_level,//分类等级
                category_list:category_list,
                category_list_v2:[],
                category_list_v3:[],
                selectionGoodsIds: [],

                search_cate_v1: search_cate_v1,
                search_cate_v2: [],
                search_cate_v3: [],

                //分页
                total: 0,
                per_size: 0,
                current_page: 0,

            }

        },
        created() {
            this.getGoodsList('{}');
        },
        methods: {
            // 获得商品列表
            getGoodsList(json) {
                var that = this;
                that.all_loading = true;
                that.$http.post('{!! yzWebFullUrl('plugin.jd-supply.admin.goods-import.goods-pagination') !!}',json).then(response => {
                    console.log(response);
                    if(response.data.result==1){
                        console.log(response);
                        that.list = response.data.data.data;
                        that.current_page = response.data.data.current_page;
                        that.per_size = response.data.data.per_page;
                        that.total = response.data.data.total;

                        that.all_loading = false;
                    } else{
                        that.$message.error(response.data.msg);
                        that.all_loading = false;
                    }
                }),function(res){
                    console.log(res);
                    that.all_loading = false;
                };
            },

            search(page) {
                this.getGoodsList({page:page,search:this.search_form});
            },

            // 上一页
            prev() {
                let page = parseInt(this.current_page) - 1;
                if (page < 1) {
                    return false;
                }

                this.getGoodsList({page:page,search:this.search_form});
            },
            //下一页
            next() {
                let page =  parseInt(this.current_page) + 1;
                if (this.per_size < this.page_number) {
                    return false;
                }

                this.getGoodsList({page:page,search:this.search_form});
            },
            //跳页
            jumpPage() {
                if (this.current_page <= 1) {
                    this.current_page = 1;
                }

                this.getGoodsList({page:this.current_page,search:this.search_form});
            },

            //选择商品
            handleSelectionChange(val) {
                var arr = [];
                for(var j = 0,len = val.length; j < len; j++){
                    arr.push(val[j].id);
                }
                this.selectionGoodsIds = arr;
                console.log(this.selectionGoodsIds);
            },

            // 一级分类改变
            changeV1(){
                this.category_form.id_v2 = "";
                this.category_form.id_v3 =  "";
                this.category_list_v2 = [];
                this.category_list_v3 = [];
                this.category_list.find(item => {
                    if(item.id == this.category_form.id_v1) {
                        this.category_list_v2 = item.childrens;
                    }
                });
            },
            // 二级分类改变
            changeV2(){
                this.category_form.id_v3 =  "";
                this.category_list_v3 = [];
                if(this.category_level==3) {
                    this.category_list_v2.find(item => {
                        if(item.id == this.category_form.id_v2) {
                            this.category_list_v3 = item.childrens;
                        }
                    })
                }
            },
            // 一级分类改变
            searchCateV2(){
                this.search_form.cate_v2 = "";
                this.search_form.cate_v3 =  "";
                this.search_cate_v2 = [];
                this.search_cate_v3 = [];
                //搜索二级分类
                var source = this.search_form.source;
                var parent_id = this.search_form.cate_v1;
                // 这里是接口请求数据, 带分页条件
                this.$http.post('{!! yzWebFullUrl('plugin.jd-supply.admin.goods-import.getChildrenCategory') !!}',{'source':source,'parent_id':parent_id}).then(response => {
                    console.log(response.data);
                    if(response.data.result==1){
                        this.search_cate_v2 = response.data.data;
                    }
                }),function(res){
                    console.log(res);
                };
            },
            // 二级分类改变
            searchCateV3(){
                this.search_form.cate_v3 =  "";
                this.search_cate_v3 = [];
                //搜索三级分类
                var source = this.search_form.source;
                var parent_id = this.search_form.cate_v2;
                // 这里是接口请求数据, 带分页条件
                this.$http.post('{!! yzWebFullUrl('plugin.jd-supply.admin.goods-import.getChildrenCategory') !!}',{'source':source,'parent_id':parent_id}).then(response => {
                    console.log(response.data);
                    if(response.data.result==1){
                        this.search_cate_v3 = response.data.data;
                    }
                }),function(res){
                    console.log(res);
                };
            },
            changeSource() {
                var that = this;
                that.search_form.cate_v1 = "";
                that.search_form.cate_v2 =  "";
                that.search_form.cate_v3 =  "";
                that.search_cate_v1 = [];
                that.search_cate_v2 = [];
                that.search_cate_v3 = [];
                var source = that.search_form.source;
                that.$http.post('{!! yzWebFullUrl('plugin.jd-supply.admin.goods-import.getChildrenCategory') !!}',{'source':source}).then(response => {
                    console.log(response.data);
                    if(response.data.result==1){
                        console.log(response);
                        that.search_cate_v1 = response.data.data;
                    }
                }),function(res){
                    console.log(res);
                };
            },
            loadmore() {
                this.category_page.pageIndex++;
                var source = this.search_form.source;
                // 这里是接口请求数据, 带分页条件
                this.$http.post('{!! yzWebFullUrl('plugin.jd-supply.admin.goods-import.getThirdPartyCategory') !!}',{'source':source,'page':this.category_page.pageIndex}).then(response => {
                    console.log(response.data);
                    if(response.data.result==1){
                        this.thirdPartyCategory = [...this.thirdPartyCategory, ...response.data.data];
                    }
                }),function(res){
                    console.log(res);
                };
            },

            // 一级标签改变
            filteringV1(){
                this.category_form.fid_v2 = "";
                this. filtering_list_v2 = [];
                this.filtering_list.find(item => {
                    if(item.id == this.category_form.fid_v1) {
                        this.filtering_list_v2 = item.value;
                    }
                });
            },

            //导入商品
            confirm()
            {
                var that = this;

                var arr = {
                    'parentid': [],
                    'childid':[],
                    'thirdid':[],
                };

                if(!this.category_form.id_v1){
                    this.$message.error("请选择要导入的商品分类");
                    return false;
                }

                if(!this.category_form.id_v2){
                    this.$message.error("请选择二级分类");
                    return false;
                }

                if(this.category_level == 3 && !this.category_form.id_v3){
                    this.$message.error("请选择三级分类");
                    return false;
                }

                if(this.category_form.fid_v1){
                    if (!this.category_form.fid_v2) {
                        this.$message.error("商品标签值不能为空");
                        return false;
                    }
                }

                arr.parentid.push(this.category_form.id_v1);

                arr.childid.push(this.category_form.id_v2);

                if(this.category_form.id_v3){
                    arr.thirdid.push(this.category_form.id_v3);
                }

                let json = {category:arr,goods_ids:this.selectionGoodsIds,f_value_id:this.category_form.fid_v2,commission_agent:this.search_form.commission_agent};

                that.is_import_disabled = true;
                that.all_loading = true;
                that.$http.post('{!! yzWebFullUrl('plugin.jd-supply.admin.goods-import.select') !!}',json).then(response => {
                    console.log(response);
                    that.is_import_disabled = true;
                    that.all_loading = false;
                    if(response.data.result==1) {
                        that.$message.success(response.data.msg);
                        that.is_import_disabled = false;
                        this.getGoodsList({page:this.current_page,search:this.search_form});
                    } else{
                        that.$message.error(response.data.msg);
                    }
                }),function(res){
                    console.log(res);
                    that.is_import_disabled = true;
                    that.all_loading = false;
                };
            }

        },
    })

</script>
@endsection('content')