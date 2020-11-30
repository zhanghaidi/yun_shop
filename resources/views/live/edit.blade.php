@extends('layouts.base')
@section('title', '直播间详情')
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('live.live-room.index')}}">直播间列表</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;直播间详情</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="{{yzWebUrl('live.live-room.edit',['id'=>$live['id']])}}" method='post' class='form-horizontal'>
                <input type="hidden" name="op" value="index">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="live"/>
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>直播间名称</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="live[name]" class="form-control" value="{{$live['name']}}" placeholder="请输入直播间名称"/></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>主播名称</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="live[anchor_name]" class="form-control" value="{{$live['anchor_name']}}" placeholder="请输入主播名称"/></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>封面图片</label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('live[cover_img]', $live['cover_img']) !!}
                                <span class="help-block">建议图片宽高比例为9:16</span>
                                @if (!empty($live['cover_img']))
                                    <a href='{{yz_tomedia($live['cover_img'])}}' target='_blank'>
                                        <img src="{{yz_tomedia($live['cover_img'])}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>分享标题</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="live[share_title]" class="form-control" value="{{$live['share_title']}}" placeholder="请输入分享标题"/></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>分享图片</label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('live[share_img]', $live['share_img']) !!}
                                <span class="help-block">建议图片宽高比例为5:4</span>
                                @if (!empty($live['share_img']))
                                    <a href='{{yz_tomedia($live['share_img'])}}' target='_blank'>
                                        <img src="{{yz_tomedia($live['share_img'])}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                    </a>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">有效期</label>
                            <div class="col-sm-9 col-xs-12">
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('live[time]', [
                               'starttime'=>$live['start_time'] ? $live['start_time'] : date('Y-m-d H:i:s') ,
                               'endtime'=>$live['end_time']  ? $live['end_time'] : date('Y-m-d H:i:s', time() + 86400),
                               'start'=> 0,
                               'end'=> 0
                               ], true) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="live[sort]" class="form-control" value="{{$live['sort'] ? $live['sort'] : 0}}" placeholder="请输入排序字段"/></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">直播间状态</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'><input type='radio' name='live[live_status]' value='0' @if (!$live['id'] || $live['live_status'] == 0) checked @endif />关闭</label>
                                <label class='radio-inline'><input type='radio' name='live[live_status]' value='101' @if ($live['live_status'] == 101) checked @endif/>直播中</label>
                                <label class='radio-inline'><input type='radio' name='live[live_status]' value='102' @if ($live['live_status'] == 102) checked @endif/>未开始</label>
                                <label class='radio-inline'><input type='radio' name='live[live_status]' value='103' @if ($live['live_status'] == 103) checked @endif/>已结束</label>
                                <label class='radio-inline'><input type='radio' name='live[live_status]' value='104' @if ($live['live_status'] == 104) checked @endif/>禁播</label>
                                <label class='radio-inline'><input type='radio' name='live[live_status]' value='105' @if ($live['live_status'] == 105) checked @endif/>暂停</label>
                                <label class='radio-inline'><input type='radio' name='live[live_status]' value='106' @if ($live['live_status'] == 106) checked @endif/>异常</label>
                                <label class='radio-inline'><input type='radio' name='live[live_status]' value='107' @if ($live['live_status'] == 107) checked @endif/>已过期</label>

                            </div>
                        </div>

                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit"  name="submit" value="提交" class="btn btn-success"/>
                                <input type="hidden" name="live[id]" value="{{$live['id']}}"/>
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