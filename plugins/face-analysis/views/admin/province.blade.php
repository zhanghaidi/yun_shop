@extends('layouts.base')
<script src="{{static_url('js/echarts.js')}}" type="text/javascript"></script>
<script src="{{static_url('js/china.js')}}" type="text/javascript"></script>

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="top">
                <ul class="add-shopnav" id="myTab">
                    <li @if($search['type'] == $value['type']) class="active" @endif><a href="{{yzWebUrl('plugin.face-analysis.admin.face-analysis-statistics.province')}}">新用户省份归属</a></li>
                </ul>
            </div>

            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group">
                    <div class="col-sm-12 col-lg-12 col-xs-12">
                        <div class="input-group">
                            <div class="input-group-addon">从新使用本服务的</div>
                            <input type="text" placeholder="输入整数" value="{{$limit}}" name="limit" class="form-control">
                            <div class="input-group-addon">名用户中</div>
                        </div>
                        <button class="btn btn-primary" type="submit">抽样</button>
                    </div>
                </div>
            </form>

            <div id="main" style="width: 1000px;height: 1000px;margin: auto;"></div>
            <script type="text/javascript">
                var provinceData = [
                    {name: '天津',value: 0 },
                    {name: '北京',value: 0 },
                    {name: '上海',value: 0 },{name: '重庆',value: 0 },
                    {name: '河北',value: 0 },{name: '河南',value: 0 },
                    {name: '云南',value: 0 },{name: '辽宁',value: 0 },
                    {name: '黑龙江',value: 0 },{name: '湖南',value: 0 },
                    {name: '安徽',value: 0 },{name: '山东',value: 0 },
                    {name: '新疆',value: 0 },{name: '江苏',value: 0 },
                    {name: '浙江',value: 0 },{name: '江西',value: 0 },
                    {name: '湖北',value: 0 },{name: '广西',value: 0 },
                    {name: '甘肃',value: 0 },{name: '山西',value: 0 },
                    {name: '内蒙古',value: 0 },{name: '陕西',value: 0 },
                    {name: '吉林',value: 0 },{name: '福建',value: 0 },
                    {name: '贵州',value: 0 },{name: '广东',value: 0 },
                    {name: '青海',value: 0 },{name: '西藏',value: 0 },
                    {name: '四川',value: 0 },{name: '宁夏',value: 0 },
                    {name: '海南',value: 0 },{name: '台湾',value: 0 },
                    {name: '香港',value: 0 },{name: '澳门',value: 0 },
                ];
                var myData = JSON.parse('{!! $map !!}');
                provinceData.map(function(value,index){
                    var realData = myData.find(function(item){
                        return item.province == value.name;
                    });

                    if(realData){
                        provinceData[index] = {name:realData.province,value:realData.num};
                    }
                });

                var optionMap = {
                    backgroundColor: '#FFFFFF',
                    title: {
                        text: '新用户省份归属',
                        subtext: '',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item'
                    },

                    //左侧小导航图标
                    visualMap: {
                        show : true,
                        x: 'left',
                        y: 'center',
                        splitList: [
                            {start: 100},{start: 50, end: 100},
                            {start: 20, end: 50},{start: 10, end: 20},
                            {start: 5, end: 10},{start: 0, end: 5},
                        ],
                        color: ['#5475f5', '#9feaa5', '#85daef','#74e2ca', '#e6ac53', '#9fb5ea']
                    },

                    //配置属性
                    series: [{
                        name: '新用户数',
                        type: 'map',
                        mapType: 'china',
                        roam: true,
                        label: {
                            normal: {
                                show: true  //省份名称
                            },
                            emphasis: {
                                show: false
                            }
                        },
                        data:provinceData  //数据
                    }]
                };
                //初始化echarts实例
                var myChart = echarts.init(document.getElementById('main'));

                //使用制定的配置项和数据显示图表
                myChart.setOption(optionMap);
            </script>

            <table class="table">
                <thead>
                    <tr>
                        <th>省/市</th>
                        <th>使用人脸检测的新会员数量</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['province']?:未知}}</td>
                        <td>{{$value['num']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr />
            <h5>使用人脸检测的新会员中，非中国的会员归属国

            <table class="table">
                <thead>
                    <tr>
                        <th>国家</th>
                        <th>数量</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($other as $value)
                    <tr>
                        <td>{{$value['nation']?:未知}}</td>
                        <td>{{$value['num']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

