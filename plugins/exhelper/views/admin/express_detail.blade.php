@extends('layouts.base')

@section('content')
@section('title', trans('快递单模版编辑'))
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<script language='javascript' src="{{resource_get('plugins/exhelper/src/common/static/js/designer.js', 1)}}"></script>
<script language='javascript' src="{{resource_get('plugins/exhelper/src/common/static/js/jquery.contextMenu.js', 1)}}"></script>
<script language='javascript' src="{{resource_get('plugins/exhelper/src/common/static/js/jquery.form.js', 1)}}"></script>
<link href="{{resource_get('plugins/exhelper/src/common/static/js/jquery.contextMenu.css', 1)}}" rel="stylesheet">
<style type='text/css'>
    #container {border: 1px solid #ccc;position: relative; background: #fff; overflow: hidden;}
    .items label {width: 120px; margin: 0; float: left;}
    #container .bg {position: absolute; width: 100%; z-index: 0;}
    #container .drag {position: absolute; min-width: 120px; min-height: 25px; border: 1px solid #aaa; z-index: 1; top: 10px; left: 100px; background: #fff; cursor: move;}
    #container .rRightDown, .rLeftDown, .rLeftUp, .rRightUp, .rRight, .rLeft, .rUp, .rDown { position: absolute; width: 7px; height: 7px; z-index: 1; font-size: 0;}
    .rRightDown, .rLeftDown, .rLeftUp, .rRightUp, .rRight, .rLeft, .rUp, .rDown {position: absolute; background: #428bca; width: 6px; height: 6px; z-index: 5; font-size: 0;}
    .rLeftDown, .rRightUp {cursor: ne-resize;}
    .rRightDown, .rLeftUp {cursor: nw-resize;}
    .rRight, .rLeft {cursor: e-resize;}
    .rUp, .rDown {cursor: n-resize;}
    .rRightDown {bottom: -3px; right: -3px; background: #2a6496;}
    .rLeftDown {bottom: -3px; left: -3px;}
    .rRightUp {top: -3px; right: -3px;}
    .rLeftUp {top: -3px; left: -3px;}
    .rRight {right: -3px; top: 50%; margin-top: -3px;}
    .rLeft {left: -3px; top: 50%; margin-top: -3px;}
    .rUp {top: -3px; left: 50%;}
    .rDown {bottom: -3px; left: 50%}
    .context-menu-layer {z-index: 9999;}
    .context-menu-list {z-index: 9999;}
    .items .checkbox-inline, .col-xs-12 .checkbox-inline {margin: 0; float: left; width: 100px;}
</style>
<div class="main rightlist">
    <form id='dataform' action="" method="post" class="form-horizontal form">
        <input type="hidden" name="id" value="{{$item->id}}" />
        <input type="hidden" name="cate" value="{{$cate}}" />
        <input type="hidden" id="datas" name="datas" value="" />
        <input type="hidden" id="expresscom" name="expresscom" value="" />
        <div class="panel panel-default">
            <div class="panel-heading">快递单模版编辑</div>
            <div class="panel-body">
                <div>
                    <div class="input-group">
                        <div class="input-group-addon" style="border-right:none">快递单名称</div>
                        <input type="text" name="expressname" class="form-control" value="{{$item->expressname}}" />
                        <div class="input-group-addon" style="border-right:none; border-left: none;"> 单据宽度</div>
                        <input type="number" name="width" class="form-control" value="@if(!empty($item->width)){{$item->width}}@else{{250}}@endif" onchange="pagesize()" />
                        <div class="input-group-addon" style="border-right:none; border-left: none;">mm(毫米) 单据高度</div>
                        <input type="number" name="height" class="form-control" value="@if(!empty($item->height)){{$item->height}}@else{{150}}@endif" onchange="pagesize()" />
                        <div class="input-group-addon" style="border-right:none; border-left: none;">mm(毫米)</div>
                        <div class="input-group-addon" style="border-right:none;">快递单底图</div>
                        <input id="bg" type="text" name="background" class="form-control" value="{{$item->bg}}" />
                        <span style="background: #fff;" class="input-group-addon btn btn-default " onclick="changeBG()">选择图片</span>
                        <span style="background: #fff;" class="input-group-addon btn btn-default " onclick="changeBG(1)">清除图片</span>
                    </div>
                    <div class="input-group" style="margin-top: 10px; width: 300px;">
                        <div class="input-group-addon" style="border-right:none">快递类型</div>
                        <select id="express" name="express" class="form-control">
                            <option @if($item->express == '') selected="" @endif data-name="" value="" >其他快递</option>
                            <option @if($item->express == 'shunfeng') selected="" @endif data-name="顺丰" value="shunfeng">顺丰</option>
                            <option @if($item->express == 'shentong') selected="" @endif data-name="申通" value="shentong">申通</option>
                            <option @if($item->express == 'yunda') selected="" @endif data-name="韵达快运" value="yunda">韵达快运</option>
                            <option @if($item->express == 'tiantian') selected="" @endif data-name="天天快递" value="tiantian">天天快递</option>
                            <option @if($item->express == 'yuantong') selected="" @endif data-name="圆通速递" value="yuantong">圆通速递</option>
                            <option @if($item->express == 'zhongtong') selected="" @endif data-name="中通速递" value="zhongtong">中通速递</option>
                            <option @if($item->express == 'ems') selected="" @endif data-name="ems快递" value="ems">ems快递</option>
                            <option @if($item->express == 'huitongkuaidi') selected="" @endif data-name="汇通快运" value="huitongkuaidi">汇通快运</option>
                            <option @if($item->express == 'quanfengkuaidi') selected="" @endif data-name="全峰快递" value="quanfengkuaidi">全峰快递</option>
                            <option @if($item->express == 'jd') selected="" @endif data-name="京东物流" value="zhimakaimen">京东物流</option>
                            <option @if($item->express == 'guosong') selected="" @endif data-name="国送快运" value="zhimakaimen">国送快运</option>
                            <option @if($item->express == 'baishiwuliu') selected="" @endif data-name="百世快运" value="baishiwuliu">百世快运</option>
                            <option @if($item->express == 'htky') selected="" @endif data-name="百世快递" value="htky">百世快递</option>
                            <option @if($item->express == 'zhaijisong') selected="" @endif data-name="宅急送" value="zhaijisong">宅急送</option>
                            <option @if($item->express == 'aae') selected="" @endif data-name="aae全球专递" value="aae">aae全球专递</option>
                            <option @if($item->express == 'anjie') selected="" @endif data-name="安捷快递" value="anjie">安捷快递</option>
                            <option @if($item->express == 'anxindakuaixi') selected="" @endif data-name="安信达快递" value="anxindakuaixi">安信达快递</option>
                            <option @if($item->express == 'biaojikuaidi') selected="" @endif data-name="彪记快递" value="biaojikuaidi">彪记快递</option>
                            <option @if($item->express == 'bht') selected="" @endif data-name="bht" value="bht">bht</option>
                            <option @if($item->express == 'baifudongfang') selected="" @endif data-name="百福东方国际物流" value="baifudongfang">百福东方国际物流</option>
                            <option @if($item->express == 'coe') selected="" @endif data-name="中国东方（COE）" value="coe">中国东方（COE）</option>
                            <option @if($item->express == 'changyuwuliu') selected="" @endif data-name="长宇物流" value="changyuwuliu">长宇物流</option>
                            <option @if($item->express == 'datianwuliu') selected="" @endif data-name="大田物流" value="datianwuliu">大田物流</option>
                            <option @if($item->express == 'debangwuliu') selected="" @endif data-name="德邦物流" value="debangwuliu">德邦物流</option>
                            <option @if($item->express == 'dhl') selected="" @endif data-name="dhl" value="dhl">dhl</option>
                            <option @if($item->express == 'dpex') selected="" @endif data-name="dpex" value="dpex">dpex</option>
                            <option @if($item->express == 'dsukuaidi') selected="" @endif data-name="d速快递" value="dsukuaidi">d速快递</option>
                            <option @if($item->express == 'disifang') selected="" @endif data-name="递四方" value="disifang">递四方</option>
                            <option @if($item->express == 'fedex') selected="" @endif data-name="fedex（国外）" value="fedex">fedex（国外）</option>
                            <option @if($item->express == 'feikangda') selected="" @endif data-name="飞康达物流" value="feikangda">飞康达物流</option>
                            <option @if($item->express == 'fenghuangkuaidi') selected="" @endif data-name="凤凰快递" value="fenghuangkuaidi">凤凰快递</option>
                            <option @if($item->express == 'feikuaida') selected="" @endif data-name="飞快达" value="feikuaida">飞快达</option>
                            <option @if($item->express == 'guotongkuaidi') selected="" @endif data-name="国通快递" value="guotongkuaidi">国通快递</option>
                            <option @if($item->express == 'ganzhongnengda') selected="" @endif data-name="港中能达物流" value="ganzhongnengda">港中能达物流</option>
                            <option @if($item->express == 'guangdongyouzhengwuliu') selected="" @endif data-name="广东邮政物流" value="guangdongyouzhengwuliu">广东邮政物流</option>
                            <option @if($item->express == 'gongsuda') selected="" @endif data-name="共速达" value="gongsuda">共速达</option>
                            <option @if($item->express == 'hengluwuliu') selected="" @endif data-name="恒路物流" value="hengluwuliu">恒路物流</option>
                            <option @if($item->express == 'huaxialongwuliu') selected="" @endif data-name="华夏龙物流" value="huaxialongwuliu">华夏龙物流</option>
                            <option @if($item->express == 'haihongwangsong') selected="" @endif data-name="海红" value="haihongwangsong">海红</option>
                            <option @if($item->express == 'haiwaihuanqiu') selected="" @endif data-name="海外环球" value="haiwaihuanqiu">海外环球</option>
                            <option @if($item->express == 'jiayiwuliu') selected="" @endif data-name="佳怡物流" value="jiayiwuliu">佳怡物流</option>
                            <option @if($item->express == 'jinguangsudikuaijian') selected="" @endif data-name="京广速递" value="jinguangsudikuaijian">京广速递</option>
                            <option @if($item->express == 'jixianda') selected="" @endif data-name="急先达" value="jixianda">急先达</option>
                            <option @if($item->express == 'jjwl') selected="" @endif data-name="佳吉物流" value="jjwl">佳吉物流</option>
                            <option @if($item->express == 'jymwl') selected="" @endif data-name="加运美物流" value="jymwl">加运美物流</option>
                            <option @if($item->express == 'jindawuliu') selected="" @endif data-name="金大物流" value="jindawuliu">金大物流</option>
                            <option @if($item->express == 'jialidatong') selected="" @endif data-name="嘉里大通" value="jialidatong">嘉里大通</option>
                            <option @if($item->express == 'jykd') selected="" @endif data-name="晋越快递" value="jykd">晋越快递</option>
                            <option @if($item->express == 'kuaijiesudi') selected="" @endif data-name="快捷速递" value="kuaijiesudi">快捷速递</option>
                            <option @if($item->express == 'lianb') selected="" @endif data-name="联邦快递（国内）" value="lianb">联邦快递（国内）</option>
                            <option @if($item->express == 'lianhaowuliu') selected="" @endif data-name="联昊通物流" value="lianhaowuliu">联昊通物流</option>
                            <option @if($item->express == 'longbanwuliu') selected="" @endif data-name="龙邦物流" value="longbanwuliu">龙邦物流</option>
                            <option @if($item->express == 'lijisong') selected="" @endif data-name="立即送" value="lijisong">立即送</option>
                            <option @if($item->express == 'lejiedi') selected="" @endif data-name="乐捷递" value="lejiedi">乐捷递</option>
                            <option @if($item->express == 'minghangkuaidi') selected="" @endif data-name="民航快递" value="minghangkuaidi">民航快递</option>
                            <option @if($item->express == 'meiguokuaidi') selected="" @endif data-name="美国快递" value="meiguokuaidi">美国快递</option>
                            <option @if($item->express == 'menduimen') selected="" @endif data-name="门对门" value="menduimen">门对门</option>
                            <option @if($item->express == 'ocs') selected="" @endif data-name="OCS" value="ocs">OCS</option>
                            <option @if($item->express == 'peisihuoyunkuaidi') selected="" @endif data-name="配思货运" value="peisihuoyunkuaidi">配思货运</option>
                            <option @if($item->express == 'quanchenkuaidi') selected="" @endif data-name="全晨快递" value="quanchenkuaidi">全晨快递</option>
                            <option @if($item->express == 'quanjitong') selected="" @endif data-name="全际通物流" value="quanjitong">全际通物流</option>
                            <option @if($item->express == 'quanritongkuaidi') selected="" @endif data-name="全日通快递" value="quanritongkuaidi">全日通快递</option>
                            <option @if($item->express == 'quanyikuaidi') selected="" @endif data-name="全一快递" value="quanyikuaidi">全一快递</option>
                            <option @if($item->express == 'rufengda') selected="" @endif data-name="如风达" value="rufengda">如风达</option>
                            <option @if($item->express == 'santaisudi') selected="" @endif data-name="三态速递" value="santaisudi">三态速递</option>
                            <option @if($item->express == 'shenghuiwuliu') selected="" @endif data-name="盛辉物流" value="shenghuiwuliu">盛辉物流</option>
                            <option @if($item->express == 'sue') selected="" @endif data-name="速尔物流" value="sue">速尔物流</option>
                            <option @if($item->express == 'shengfeng') selected="" @endif data-name="盛丰物流" value="shengfeng">盛丰物流</option>
                            <option @if($item->express == 'saiaodi') selected="" @endif data-name="赛澳递" value="saiaodi">赛澳递</option>
                            <option @if($item->express == 'tiandihuayu') selected="" @endif data-name="天地华宇" value="tiandihuayu">天地华宇</option>
                            <option @if($item->express == 'tnt') selected="" @endif data-name="tnt" value="tnt">tnt</option>
                            <option @if($item->express == 'ups') selected="" @endif data-name="ups" value="ups">ups</option>
                            <option @if($item->express == 'wanjiawuliu') selected="" @endif data-name="万家物流" value="wanjiawuliu">万家物流</option>
                            <option @if($item->express == 'wenjiesudi') selected="" @endif data-name="文捷航空速递" value="wenjiesudi">文捷航空速递</option>
                            <option @if($item->express == 'wuyuan') selected="" @endif data-name="伍圆" value="wuyuan">伍圆</option>
                            <option @if($item->express == 'wxwl') selected="" @endif data-name="万象物流" value="wxwl">万象物流</option>
                            <option @if($item->express == 'xinbangwuliu') selected="" @endif data-name="新邦物流" value="xinbangwuliu">新邦物流</option>
                            <option @if($item->express == 'xinfengwuliu') selected="" @endif data-name="信丰物流" value="xinfengwuliu">信丰物流</option>
                            <option @if($item->express == 'yafengsudi') selected="" @endif data-name="亚风速递" value="yafengsudi">亚风速递</option>
                            <option @if($item->express == 'yibangwuliu') selected="" @endif data-name="一邦速递" value="yibangwuliu">一邦速递</option>
                            <option @if($item->express == 'youshuwuliu') selected="" @endif data-name="优速物流" value="youshuwuliu">优速物流</option>
                            <option @if($item->express == 'youzhengguonei') selected="" @endif data-name="邮政包裹挂号信" value="youzhengguonei">邮政包裹挂号信</option>
                            <option @if($item->express == 'youzhengguoji') selected="" @endif data-name="邮政国际包裹挂号信" value="youzhengguoji">邮政国际包裹挂号信</option>
                            <option @if($item->express == 'yuanchengwuliu') selected="" @endif data-name="远成物流" value="yuanchengwuliu">远成物流</option>
                            <option @if($item->express == 'yuanweifeng') selected="" @endif data-name="源伟丰快递" value="yuanweifeng">源伟丰快递</option>
                            <option @if($item->express == 'yuanzhijiecheng') selected="" @endif data-name="元智捷诚快递" value="yuanzhijiecheng">元智捷诚快递</option>
                            <option @if($item->express == 'yuntongkuaidi') selected="" @endif data-name="运通快递" value="yuntongkuaidi">运通快递</option>
                            <option @if($item->express == 'yuefengwuliu') selected="" @endif data-name="越丰物流" value="yuefengwuliu">越丰物流</option>
                            <option @if($item->express == 'yad') selected="" @endif data-name="源安达" value="yad">源安达</option>
                            <option @if($item->express == 'yinjiesudi') selected="" @endif data-name="银捷速递" value="yinjiesudi">银捷速递</option>
                            <option @if($item->express == 'zhongtiekuaiyun') selected="" @endif data-name="中铁快运" value="zhongtiekuaiyun">中铁快运</option>
                            <option @if($item->express == 'zhongyouwuliu') selected="" @endif data-name="中邮物流" value="zhongyouwuliu">中邮物流</option>
                            <option @if($item->express == 'zhongxinda') selected="" @endif data-name="忠信达" value="zhongxinda">忠信达</option>
                            <option @if($item->express == 'zhimakaimen') selected="" @endif data-name="芝麻开门" value="zhimakaimen">芝麻开门</option>

                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel" style="height: auto; overflow: hidden; background: none;">

            @include('Yunshop\Exhelper::admin.print.print_tpl_items')

            <div class="edit-right">
                <div id="container" style=" height: @if($item->height && !empty(!$item->height)){{$item->height.'mm'}}@else{{'150mm'}}@endif;">
                    <img id="bgimg" src="{{$item->bg}}" @if(empty($item->bg)) style="display: none;"@endif />
                    @foreach($item->datas as $key => $value)
                    <div class="drag" cate="{{$value['cate']}}" index="{{$key}}" items="{{$value['items']}}" item-string="{{$value['string']}}" item-font="{{$value['font']}}" item-size="{{$value['size']}}" item-color="{{$value['color']}}" item-bold="{{$value['bold']}}" item-pre="{{$value['pre']}}" item-last="{{$value['last']}}" item-align="{{$value['align']}}"
                         style="font-size:{{$value['size']}}pt; z-index:{{$key}};left:{{$value['left']}};top:{{$value['top']}};width:{{$value['width']}};height:{{$value['height']}}; text-align:@if($value['align'] == '' || $value['align'] == 1) left @elseif($value['align'] == 2) center @elseif($value['align'] == 3) right @endif" item-virtual="{{$value['virtual']}}" cate="{{$value['cate']}}">
                        <div class="text" style="@if(!empty($value['font']))font-family: {{$value['font']}};@endif font-size:@if(!empty($value['size'])){{$value['size']}}@else{{10}}@endif pt;@if(!empty($value['color']))color: {{$value['color']}};@endif @if(!empty($value['bold']))font-weight:bold;@endif">
                            {{$value['pre']}}{{$value['string']}}{{$value['last']}}
                        </div>
                        <div class="rRightDown"> </div>
                        <div class="rLeftDown"> </div>
                        <div class="rRightUp"> </div>
                        <div class="rLeftUp"> </div>
                        <div class="rRight"> </div>
                        <div class="rLeft"> </div>
                        <div class="rUp"> </div>
                        <div class="rDown"></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class='panel-body'>
            <div class="form-group">
                <div class="col-sm-9 col-xs-12">
                    <a href="{{yzWebUrl(\Yunshop\Exhelper\common\models\Express::EXPRESS_INDEX_URL)}}"><span  class="btn btn-default" style="float: left; margin-right: 10px;"><i class="fa fa-reply"></i> 返回列表</span></a>

                    <input type="button" name="btnsave" value="保 存" class="btn btn-primary col-lg btnsave" onclick="save(false)" />
                    <input type="button" name="btnpreview" value="保存并预览" class="btn btn-success col-lg btnsave" onclick="save(true)" style="margin-left:10px;" />
                    <div style="float: left; margin-left: 10px;">
                        <label class="radio-inline">
                            <input type="radio" name='isdefault' value="1" @if($item->isdefault == 1) checked @endif /> 设为默认打印模版
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name='isdefault' value="0" @if($item->isdefault == 0) checked @endif /> 不设为默认打印模版
                        </label>
                    </div>
                    @if($item->isdefault == 1)<span class="help-block">默认模版</span>@endif
                </div>
            </div>
        </div>
    </form>
</div>
<script language='javascript' src="{{resource_get('plugins/exhelper/src/common/static/js/LodopFuncs.js', 1)}}"></script>
{{--<script src='http://@if(empty($print_set->id)){{8000}}@else{{$print_set->id}}@endif:@if(empty($print_set->port)){{8000}}@else{{$print_set->port}}@endif/CLodopfuncs.js'></script>--}}
<script language='javascript'>
        function pagesize(){
            var _width = $("input[name=width]").val();
            var _height = $("input[name=height]").val();
            if(_width<10){
                alert("最低宽度不小于10");
                $("input[name=width]").val("10");
                return;
            }
            if(_width>400){
                alert("最大宽度不大于400");
                $("input[name=width]").val("400");
                return;
            }
            $("#container").height(_height+"mm").width(_width+"mm");
        }
        function delInput(){
            var _index = currentDrag.attr("index");
            $(".drag").each(function(){
                var index = $(this).attr("index");
                if(index==_index){
                    $(this).remove();
                }
            });
            $(".deleteinput").hide();
        }
        function addInput(n) {
            var index = $('#container .drag').length + 1;
            if(n==1){
                var data = '<div class="drag" cate="2" style="width: auto; height: auto; padding: 10px;" index="' + index + '" style="z-index:' + index + '" fields="" item-size="12" item-font="微软雅黑" item-align="1">';
                data+='<div class="text-table">';
                data+='请在左侧先选择列';
                data+='</div>';
                data+='<div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"> </div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div>';
                data+='</div>';
                var drag = $(data)
            }else{
                var drag = $('<div class="drag" cate="1" index="' + index + '" style="z-index:' + index + '" fields="" item-size="12" item-font="微软雅黑" item-align="1"><div class="text" style="font-size:12pt"></div><div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"> </div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div></div>');
            }
            $('#container').append(drag);
            // todo 加上就报错
            bindEvents(drag);
            setCurrentDrag(drag);
        }
        function changeBG(n) {
            if(n){
                $("#bgimg").attr("src","").hide();
                $("#bg").val("");
            }else{
                util.image('', function(data) {
                    $("#bgimg").attr("src",data.url).show();
                    $("#bg").val(data.url);
                });
            }
        }
        var currentDrag = false;

        function bindEvents(obj) {
            var index = obj.attr('index');
            var rs = new Resize(obj, {
                Max: true,
                mxContainer: "#container"
            });
            rs.Set($(".rRightDown", obj), "right-down");
            rs.Set($(".rLeftDown", obj), "left-down");
            rs.Set($(".rRightUp", obj), "right-up");
            rs.Set($(".rLeftUp", obj), "left-up");
            rs.Set($(".rRight", obj), "right");
            rs.Set($(".rLeft", obj), "left");
            rs.Set($(".rUp", obj), "up");
            rs.Set($(".rDown", obj), "down");
            new Drag(obj, {
                Limit: true,
                mxContainer: "#container"
            });
            $('.drag .remove').unbind('click').click(function() {
                $(this).parent().remove();
            });
            $.contextMenu({
                selector: '.drag[index=' + index + ']',
                callback: function(key, options) {
                    var index = $(this).attr('index');
                    if (key == 'next') {
                        var nextdiv = $(this).next('.drag');
                        if (nextdiv.length > 0) {
                            nextdiv.insertBefore($(this));
                        }
                    }
                    else if (key == 'prev') {
                        var prevdiv = $(this).prev('.drag');
                        if (prevdiv.length > 0) {
                            $(this).insertBefore(prevdiv);
                        }
                    }
                    else if (key == 'last') {
                        var len = $('.drag').length;
                        if (index >= len - 1) {
                            return;
                        }
                        var last = $('#container .drag:last');
                        if (last.length > 0) {
                            $(this).insertAfter(last);
                        }
                    }
                    else if (key == 'first') {
                        var index = $(this).index();
                        if (index <= 1) {
                            return;
                        }
                        var first = $('#container .drag:first');
                        if (first.length > 0) {
                            $(this).insertBefore(first);
                        }
                    }
                    else if (key == 'delete') {
                        $(this).remove();
                        $('.items').hide();
                        $(".item-tip").show();
                        $(".deleteinput").hide();
                    }
                    var n = 1;
                    $('.drag').each(function() {
                        $(this).css("z-index", n);
                        n++;
                    })
                },
                items: {"next": {name: "调整到上层"},"prev": {name: "调整到下层"},"last": {name: "调整到最顶层"},"first": {name: "调整到最低层"},"delete": {name: "删除元素"}}
            });
            obj.unbind('mousedown').mousedown(function() {
                setCurrentDrag(obj);
            });
        }
        var timer = 0;
        function setCurrentDrag(obj) {
            currentDrag = obj;
            var cate = obj.attr('cate');
            bindItems();
            $(".item-tip").hide();
            $('.items').show();
            $(".deleteinput").show();
            $(".cate1").show();
            $(".cate2").hide();
            $('.drag').css('border', '1px solid #aaa');
            obj.css('border', '1px solid #428bca');
        }
        function bindItems() {
            var items = currentDrag.attr('items') || "";
            var values = items.split(',');
            $('.items').find(':checkbox').each(function() {
                $(this).get(0).checked = false;
            });
            $('#item-font').val('');
            $('#item-size').val('');
            $('#item-bold').val('');
            for (var i in values) {
                if (values[i] != '') {
                    $('.items').find(":checkbox[value='" + values[i] + "']").get(0).checked = true;
                }
            }
            $('#item-font').val(currentDrag.attr('item-font') || '');
            $('#item-size').val(currentDrag.attr('item-size') || '');
            $('#item-bold').val(currentDrag.attr('item-bold') || '');
            $('#item-pre').val(currentDrag.attr('item-pre') || '');
            $('#item-last').val(currentDrag.attr('item-last') || '');
            $('#item-align').val(currentDrag.attr('item-align') || '');
            var itemcolor = $('#item-color');
            var input = itemcolor.find('input:first');
            var picker = itemcolor.find('.sp-preview-inner');
            var color = currentDrag.attr('item-color') || '#000';
            input.val(color);
            picker.css({
                'background-color': color
            });
            timer = setInterval(function() {
                var cate = currentDrag.attr("cate");
                currentDrag.attr('item-color', input.val()).find('.text').css('color', input.val());
                currentDrag.attr('item-pre', $('#item-pre').val());
                currentDrag.attr('item-last', $('#item-last').val());
                var pre = currentDrag.attr('item-pre') || "";
                var last = currentDrag.attr('item-last') || "";
                var string = currentDrag.attr('item-string') || "";
                currentDrag.find('.text').html(pre + string + last);
            }, 10);
        }
        $(function() {
            $('#dataform').ajaxForm();
            $('.drag').each(function() {
                bindEvents($(this));
            })

            $('.items .checkbox-inline').click(function(e) {
                if( $(e.target).find('input').length>0){
                    return;
                }
                if (currentDrag) {
                    var cate = currentDrag.attr("cate");
                    var values = [];
                    var titles = [];
                    var datas = [];
                    var vd = [];
                    $('.items').find(':checkbox:checked').each(function() {
                        var _titles = $(this).attr('title');
                        var _values = $(this).val();
                        var _vd = $(this).data('vd');
                        titles.push(_titles);
                        values.push(_values);
                        vd.push(_vd);
                        datas.push({"value":_values,"title":_titles,"vd":_vd});
                    });
                    currentDrag.attr('items', values.join(',')).attr('item-string', titles.join(',')).find('.text').text(titles.join(','));
                }
            });
            $('#item-font').change(function() {
                if (currentDrag) {
                    var cate = currentDrag.attr("cate");
                    var data = $(this).val();
                    currentDrag.attr('item-font', data);
                    if (data == '') {
                        data = "微软雅黑";
                    }
                    currentDrag.attr('item-font', data).find(".text").css('font-family', data);
                }
            });
            $('#item-align').change(function() {
                if (currentDrag) {
                    var cate = currentDrag.attr("cate");
                    var data = $(this).val();
                    currentDrag.attr('item-align', data);
                    if (data == '') {
                        data = "1";
                    }
                    var str = '';
                    if (data == 1) {
                        str = "left";
                    }
                    if (data == 2) {
                        str = "center";
                    }
                    if (data == 3) {
                        str = "right";
                    }
                    currentDrag.attr('item-font', data).find(".text").css('text-align', str);
                }
            });
            $('#item-size').change(function() {
                if (currentDrag) {
                    var cate = currentDrag.attr("cate");
                    var data = $(this).val();
                    currentDrag.attr('item-size', data);
                    if (data == '') {
                        data = 12;
                    }
                    currentDrag.find(".text").css('font-size', data + "pt");
                }
            });
            $('#item-bold').change(function() {
                if (currentDrag) {
                    var cate = currentDrag.attr("cate");
                    var data = $(this).val();
                    currentDrag.attr('item-bold', data);
                    if (data == 'bold') {
                        currentDrag.css('font-weight', 'bold');
                    } else {
                        currentDrag.find(".text").css('font-weight', 'normal');
                    }
                }
            });
        });

        function save(ispreview) {
            var data = [];
            $('.drag').each(function() {
                var obj = $(this);
                var d = {
                    left: obj.css('left'),
                    top: obj.css('top'),
                    width: obj.css('width'),
                    height: obj.css('height'),
                    items: obj.attr('items'),
                    font: obj.attr('item-font'),
                    size: obj.attr('item-size'),
                    color: obj.attr('item-color'),
                    bold: obj.attr('item-bold'),
                    string: obj.attr('item-string'),
                    pre: obj.attr('item-pre'),
                    last: obj.attr('item-last'),
                    align: obj.attr('item-align'),
                    cate: obj.attr('cate'),
                    virtual: obj.attr('item-virtual')
                };
                data.push(d);
            });
            console.log(data);
            console.log(JSON.stringify(data));
            $("#expresscom").val($("#express").find("option:selected").data("name"));
            $('#datas').val(JSON.stringify(data));
            $('.btnsave').button('loading');
            $('#dataform').ajaxSubmit(function(res) {
                $('.btnsave').button('reset');
                console.log(res);
                //data = eval("(" + data + ")");
                //$(':hidden[name=id]').val(data.id);
                if (ispreview) {
                    previews();
                } else {
                    if (res == 'success'){
                        console.log('ok...');
                        location.href = "{!! yzWebUrl(\Yunshop\Exhelper\common\models\Express::EXPRESS_INDEX_URL) !!}";
                    }
                }
            })
            return;
        }

        function previews() {
            var LODOP=getCLodop();
            alert("保存成功！正在生成预览。。。");
            var Width = $("input[name=width]").val()+"pt";
            var Height = $("input[name=height]").val()+"pt";
            LODOP.PRINT_INITA(0,0,Width,Height,"快递单预览"); //1188
            LODOP.ADD_PRINT_HTM(0, 0, 869, 480, $("#container").html());
            LODOP.PREVIEW();
        }
</script>
@endsection