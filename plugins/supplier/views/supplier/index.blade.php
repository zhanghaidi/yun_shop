<script src="{{static_url('js/echarts.js')}}" type="text/javascript"></script>
<style>
    .main-panel>.content{background:#f5f5f5 !important;padding:0}
    body{background:#f5f5f5 !important}
    .all{background:#f5f5f5}
    .chart{background:#fff;margin:15px 0;border-radius:10px;}
    .plugin-box{padding:15px 0;width:calc(100% / 8);display:inline-block;text-align:center}
    .plugin-box-a{color:#000;}
    .plugin-icon{background:#29ba9b;width:80px;height:80px;color:#fff;text-align:center;border-radius:10px;display:inline-block;}
    .plugin-icon-i{}
    .plugin-name{line-height:36px;font-weight:800;font-size:16px;}
    .plugin-name-1{font-size:14px;}
    .plugin-title{padding-left:20px;}

    .total-box{padding:15px 0;width:calc(100% / 5 - 4px);display:inline-block;text-align:center;border-right:2px solid #ccc;}
    .total-box-noboder{border-right:0px solid #ccc}
    .total-box-text{font-size:22px;color:#29ba9b;font-weight:900}
    .total-box-text1{color:#ffd07c;}
    [v-cloak]{display:none}
</style>
@extends('layouts.base')

@section('content')
    <div class="all">
        <div id="app" v-cloak v-loading="loading">
            <div class="chart total">
                <div class="total-box">
                    <div>
                        <count-to class="total-box-text total-box-text1" :start-val='0' :end-val="today_money" :decimals='2' :duration=4000></count-to>
                    </div>
                    <div class="plugin-name plugin-name-1">
                        今日交易额（元）
                    </div>
                </div>
                <div class="total-box">
                    <div>
                        <count-to class="total-box-text total-box-text1" :decimals='2' :start-val='0' :end-val="all_money" :duration=4000></count-to>
                    </div>
                    <div  class="plugin-name plugin-name-1">
                       累计交易额（元）
                    </div>
                </div>
                <div class="total-box">
                    <div>
                        <count-to class="total-box-text total-box-text1" :start-val='0' :end-val="today_order" :duration=4000></count-to>
                    </div>
                    <div  class="plugin-name plugin-name-1">
                        今日订单数（单）
                    </div>
                </div>
                <div class="total-box">
                    <div>
                        <count-to class="total-box-text total-box-text1" :start-val='0' :end-val="waitPayOrder" :duration=4000></count-to>
                    </div>
                    <div  class="plugin-name plugin-name-1">
                        待付款订单（单）
                    </div>
                </div>
                <div class="total-box total-box-noboder">
                    <div>
                        <count-to class="total-box-text total-box-text1" :start-val='0' :end-val="waitSendOrder" :duration=4000></count-to>
                    </div>
                    <div  class="plugin-name plugin-name-1">
                        待发货订单（单）
                    </div>
                </div>
                
            </div>
            <div class="chart">
                <div class="plugin-name plugin-title">订单趋势</div>
                <div ref="chartmain" style="width:100%; height: 400px;margin:0;padding:0;margin-top:20px;"></div>
            </div>
            <div class="chart plugin" v-if="plugins.length != 0">
                <div class="plugin-name plugin-title">常用功能</div>
                <div v-for="(item,index) in plugins" :key="index" class="plugin-box">
                    <a :href="item.url" class="plugin-box-a">
                        <div class="plugin-icon">
                            <i class="fa" :class="item.icon" style="font-size:50px;margin-top:15px;"></i>
                            <!-- <img :src="item.icon_url" style="font-size:50px;width:80px;height:80px;"> -->
                        </div>
                        <div class="plugin-name">
                            [[item.name]]
                        </div>
                    </a>
                </div>
            </div>
            <div class="example-item">
            </div>
        </div>
    </div>

    <script>
        var app = new Vue({
            el:"#app",
            delimiters: ['[[', ']]'],
            name: 'test',
            data() {

                return{
                    chart_data:[],
                    plugins:[],
                    list:[{}],
                    
                    all_money:'',
                    today_money:'',
                    today_order:'',
                    waitPayOrder:'',
                    waitSendOrder:'',
                    loading:false,
                }
            },
            created() {
                
                let data = {!! $data?:'{goods:[],order:{}}' !!};
                console.log(data)
                this.setData(data);


            },
            mounted() {
                this.getRef();

            },
            methods: {
                setData(data) {
                    this.loading = true;

                    this.chart_data = data.week;
                    this.plugins = data.plugins;
                    this.all_money = Number(data.all_money);
                    this.today_money = Number(data.today_money);
                    this.today_order = Number(data.today_order);
                    this.waitPayOrder = Number(data.waitPayOrder);
                    this.waitSendOrder = Number(data.waitSendOrder);
                    let obj = data.menu;
                    let arr = [];
                    for(let i in obj) {
                        console.log(obj[i]);
                        arr.push({name:obj[i].name,icon:obj[i].icon,url:obj[i].url})
                    }
                    console.log(arr)
                    this.plugins = arr;
                    this.loading = false;

                },
                getData() {
                    this.loading = true;
                    this.$http.post('{!! yzWebFullUrl('survey.survey.survey') !!}').then(function (response) {
                            if (response.data.result){
                                this.setData(response.data)
                            }
                            else {
                                this.$message({message: response.data.msg,type: 'error'});
                            }
                            this.loading = false;
                        },function (response) {
                            this.$message({message: response.data.msg,type: 'error'});
                            this.loading = false;
                        }
                    );
                },
                getRef() {
                    //指定图标的配置和数据
                    var option = {
                        title:{
                            text:''
                        },
                        tooltip:{},
                        legend:{
                            data:['总订单','已完成','已发货']
                        },
                        xAxis:{
                            data:[this.chart_data[0].data,this.chart_data[1].data,this.chart_data[2].data,this.chart_data[3].data,this.chart_data[4].data,this.chart_data[5].data,this.chart_data[6].data]
                        },
                        yAxis:{

                        },
                        series:[
                            {name: ['总订单'],type:'line',data:[this.chart_data[0].week_order,this.chart_data[1].week_order,this.chart_data[2].week_order,this.chart_data[3].week_order,this.chart_data[4].week_order,this.chart_data[5].week_order,this.chart_data[6].week_order]},
                            {name: ['已完成'],type:'line',data:[this.chart_data[0].completed_order,this.chart_data[1].completed_order,this.chart_data[2].completed_order,this.chart_data[3].completed_order,this.chart_data[4].completed_order,this.chart_data[5].completed_order,this.chart_data[6].completed_order]},
                            {name: ['已发货'],type:'line',data:[this.chart_data[0].send_order,this.chart_data[1].send_order,this.chart_data[2].send_order,this.chart_data[3].send_order,this.chart_data[4].send_order,this.chart_data[5].send_order,this.chart_data[6].send_order]},
                        ]
                    };
                    //初始化echarts实例
                    // var myChart = echarts.init(document.getElementById('chartmain'));
                    var myChart1 = echarts.init(this.$refs.chartmain);
                    //使用制定的配置项和数据显示图表
                    myChart1.setOption(option);
                },
                // 字符转义
                escapeHTML(a) {
                    a = "" + a;
                    return a.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, "\"").replace(/&apos;/g, "'");;
                },
            },
        })

    </script>
@endsection

