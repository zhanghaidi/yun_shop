@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
            <div id="main" style="width: 2260px;height:1200px;"></div>
        </div>
    </div>
</div>

<script language="JavaScript">
var keywords =[{name:'足三里',value:1}];

_url = "{{ yzWebUrl('plugin.minapp-content.admin.search.lists') }}";
_url = _url.replace(/&amp;/g, '&');
$.get(_url, function(res){
    console.log(res);
    if (('result' in res) && res.result == 1) {
        keywords = res.data;
        $(function(){
            require({
                paths: {
                    'echarts': '/addons/yun_shop/plugins/minapp-content/resource/js/echarts/echarts.min',
                    'wordcloud': '/addons/yun_shop/plugins/minapp-content/resource/js/echarts/echarts-wordcloud.min',
                }
            })
            require(['echarts','wordcloud'], function(echarts,wordcloud) {
                // 基于准备好的dom，初始化echarts实例 随机颜色 词云图需要额外引入 词云图js https://oisanjavax.github.io/echarts-wordcloud/dist/echarts-wordcloud.min.js
                let randcolor = () => {
                    let r = 100 + ~~(Math.random() * 100);
                    let g = 135 + ~~(Math.random() * 100);
                    let b = 100 + ~~(Math.random() * 100);
                    return `rgb(${r}, ${g}, ${b})`
                }
                option = {
                    backgroundColor: 'rgba(0,0,0,.5)',
                    tooltip: {
                        trigger: 'item',
                        padding: [10, 15],
                        textStyle: {
                            fontSize: 20
                        },
                        formatter: params => {
                            const {name, value} = params
                            return `关键词：${name} <br/>次数：${value}`
                        }
                    },
                    series: [{
                        type: 'wordCloud',
                        gridSize: 20,
                        sizeRange: [12, 50],
                        rotationRange: [0, 0],
                        shape: 'circle',
                        textStyle: {
                            normal: {
                                color: params => {
                                    return randcolor()
                                }
                            },
                            emphasis: {
                                shadowBlur: 10,
                                shadowColor: '#333'
                            }
                        },
                        data: keywords
                    }]
                };
                echarts.init(document.getElementById('main')).setOption(option);
            });
        });
    } else {
        util.message('请求失败');
    }
});
</script>
@endsection
