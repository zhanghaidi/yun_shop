@extends('layouts.base')
@section('title', '弹窗详情')
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('plugin.min-app.Backend.Controllers.popup.index')}}">弹窗列表</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;弹窗详情</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="{{yzWebUrl('plugin.min-app.Backend.Controllers.popup.edit',['id'=>$popup['id']])}}" method='post' class='form-horizontal'>
                <input type="hidden" name="op" value="index">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="popup"/>
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">弹窗名称</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="popup[title]" class="form-control" value="{{$popup['title']}}" placeholder="请输入弹窗名称"/></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >弹窗位置</label>
                            <div class="col-sm-9 col-xs-12">
                                <select name="popup[position_id]" class="form-control">
                                    @foreach($position as $item)
                                        <option @if(!empty($popup['position_id']) && $popup['position_id'] == $item['id'])) selected @endif value="{{$item['id']}}">{{$item['position_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>弹窗图片</label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('popup[picture]', $popup['picture']) !!}
                                <span class="help-block">建议图片宽高比例为4:5</span>
                                @if (!empty($popup['picture']))
                                    <a href='{{yz_tomedia($popup['picture'])}}' target='_blank'>
                                        <img src="{{yz_tomedia($popup['picture'])}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                    </a>
                                @endif
                            </div>
                        </div>

{{--                        <div class="form-group">--}}
{{--                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">网页链接地址</label>--}}
{{--                            <div class="col-sm-9 col-xs-12"><input type="text" name="popup[web_link]" class="form-control" value="{{$popup['web_link']}}" placeholder="请输入网页链接地址"/></div>--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">小程序路径</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="popup[pagepath]" class="form-control" value="{{$popup['pagepath']}}" placeholder="请输入小程序路径" /></div>
                        </div>

{{--                        <div class="form-group">--}}
{{--                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示规则</label>--}}
{{--                            <div class="col-sm-9 col-xs-12">--}}
{{--                                @foreach($show_rule as $key => $item)--}}
{{--                                    <label class='radio-inline'><input type='radio' name='popup[show_rule]' value='{{$key}}' @if ($popup['show_rule'] == $key) checked @endif/>{{$item}}</label>--}}
{{--                                @endforeach--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">有效期</label>
                            <div class="col-sm-9 col-xs-12">
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('popup[time]', [
                               'starttime'=>$popup['start_time'],
                               'endtime'=>$popup['end_time'],
                               'start'=>0,
                               'end'=>0
                               ], true) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                            <div class="col-sm-9 col-xs-12"><input type="number" name="popup[sort]" class="form-control" value="{{$popup['sort']}}"/></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'><input type='radio' name='popup[is_show]' value='1' @if (!$popup['id'] || $popup['is_show'] == 1) checked @endif/>开启</label>
                                <label class='radio-inline'><input type='radio' name='popup[is_show]' value='0' @if ($popup['id'] && $popup['is_show'] == 0) checked @endif />关闭</label>
                            </div>
                        </div>

                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit"  name="submit" value="提交" class="btn btn-success"/>
                                <input type="hidden" name="popup[id]" value="{{$popup['id']}}"/>
                                <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;'/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>

    </script>
@endsection