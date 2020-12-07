@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">{{$pluginName}}记录</div>
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group">
                    <div class="col-sm-12 col-lg-12 col-xs-12">
                        <div class="input-group">
                            <div class="input-group-addon">性别:</div>
                            <select class="form-control" name="search[gender]">
                                <option value="">全部</option>
                                <option value="1" @if($search['gender'] == 1) selected="selected" @endif>女</option>
                                <option value="2" @if($search['gender'] == 2) selected="selected" @endif>男</option>
                            </select>
                            <div class="input-group-addon">年龄: 从</div>
                            <input type="text" placeholder="开始年龄(整数)" value="{{$search['age_start']}}" name="search[age_start]" class="form-control">
                            <div class="input-group-addon"> - </div>
                            <input type="text" placeholder="结束年龄(整数)" value="{{$search['age_end']}}" name="search[age_end]" class="form-control">
                            <div class="input-group-addon">岁 , 魅力: 从</div>
                            <input type="text" placeholder="开始魅力(整数)" value="{{$search['beauty_start']}}" name="search[beauty_start]" class="form-control">
                            <div class="input-group-addon"> - </div>
                            <input type="text" placeholder="结束魅力(整数)" value="{{$search['beauty_end']}}" name="search[beauty_end]" class="form-control">
                            <div class="input-group-addon"> , 笑脸: 从</div>
                            <input type="text" placeholder="开始数(整数)" value="{{$search['expression_start']}}" name="search[expression_start]" class="form-control">
                            <div class="input-group-addon"> - </div>
                            <input type="text" placeholder="结束数(整数)" value="{{$search['expression_end']}}" name="search[expression_end]" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12 col-lg-12 col-xs-12">
                        <div class="input-group">
                            <div class="input-group-addon">帽子:</div>
                            <select class="form-control" name="search[hat]">
                                <option value="">全部</option>
                                <option value="9" @if($search['hat'] === '0') selected="selected" @endif>无</option>
                                <option value="1" @if($search['hat'] === '1') selected="selected" @endif>有</option>
                            </select>
                            <div class="input-group-addon">眼镜:</div>
                            <select class="form-control" name="search[glass]">
                                <option value="">全部</option>
                                <option value="9" @if($search['glass'] === '0') selected="selected" @endif>无</option>
                                <option value="1" @if($search['glass'] === '1') selected="selected" @endif>有</option>
                            </select>
                            <div class="input-group-addon">口罩:</div>
                            <select class="form-control" name="search[mask]">
                                <option value="">全部</option>
                                <option value="9" @if($search['mask'] === '0') selected="selected" @endif>无</option>
                                <option value="1" @if($search['mask'] === '1') selected="selected" @endif>有</option>
                            </select>
                            <div class="input-group-addon">头发长度:</div>
                            <select class="form-control" name="search[hair-length]">
                                <option value="">全部</option>
                                <option value="9" @if($search['hair-length'] === '0') selected="selected" @endif>光头</option>
                                <option value="1" @if($search['hair-length'] === '1') selected="selected" @endif>短发</option>
                                <option value="2" @if($search['hair-length'] === '2') selected="selected" @endif>中发</option>
                                <option value="3" @if($search['hair-length'] === '3') selected="selected" @endif>长发</option>
                                <option value="4" @if($search['hair-length'] === '4') selected="selected" @endif>绑发</option>
                            </select>
                            <div class="input-group-addon">刘海:</div>
                            <select class="form-control" name="search[hair-bang]">
                                <option value="">全部</option>
                                <option value="9" @if($search['hair-bang'] === '0') selected="selected" @endif>无</option>
                                <option value="1" @if($search['hair-bang'] === '1') selected="selected" @endif>有</option>
                            </select>
                            <div class="input-group-addon">发色:</div>
                            <select class="form-control" name="search[hair-color]">
                                <option value="">全部</option>
                                <option value="9" @if($search['hair-color'] === '0') selected="selected" @endif>黑色</option>
                                <option value="1" @if($search['hair-color'] === '1') selected="selected" @endif>金色</option>
                                <option value="2" @if($search['hair-color'] === '2') selected="selected" @endif>棕色</option>
                                <option value="3" @if($search['hair-color'] === '3') selected="selected" @endif>灰白色</option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-12 col-lg-12 col-xs-12">
                        <div class="input-group">
                            <div class="input-group-addon">检测时间范围:</div>
<script type="text/javascript">
    require(["daterangepicker"], function($) {
        $(function() {
            $(".daterange.daterange-time").each(function() {
                var elm = this;
                $(this).daterangepicker({
                    startDate: $(elm).prev().prev().val(),
                    endDate: $(elm).prev().val(),
                    format: "YYYY-MM-DD HH:mm",
                    timePicker: true,
                    timePicker12Hour: false,
                    timePickerIncrement: 1,
                    minuteStep: 1
                }, function(start, end) {
                    $(elm).find(".date-title").html(start.toDateTimeStr() + " 至 " + end.toDateTimeStr());
                    $(elm).prev().prev().val(start.toDateTimeStr());
                    $(elm).prev().val(end.toDateTimeStr());
                });
            });
        });
    });
</script>
                            <input type="hidden" value="{{$search['time_start']}}" name="search[time_start]">
                            <input type="hidden" value="{{$search['time_end']}}" name="search[time_end]">
                            <button type="button" class="btn btn-default daterange daterange-time" data-original-title="" title="">
                                <span class="date-title">{{$search['time_start']}} 至 {{$search['time_end']}}</span>
                                <i class="fa fa-calendar"></i>
                            </button>

                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="5%">上传图片</th>
                        <th width="10%">用户昵称 - 手机号</th>
                        <th width="5%">性别</th>
                        <th width="5%">年龄</th>
                        <th width="5%">魅力</th>
                        <th width="5%">笑的程度</th>
                        <th width="5%">帽子</th>
                        <th width="5%">眼镜</th>
                        <th width="5%">口罩</th>
                        <th width="5%">头发</th>
                        <th width="5%">刘海</th>
                        <th width="5%">发色</th>
                        <th width="5%">花费</th>
                        <th width="5%">赠送</th>
                        <th width="10%">检测时间</th>
                        <th width="5%">编辑</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr style="height: 90px;">
                        <td>{{$value['id']}}</td>
                        <td><a href="{{$value['url']}}" target="_blank"><img src="{{$value['url']}}" height="90"></a></td>
                        <td>{{$value['nickname']}} @if($value['mobile']) - {{$value['mobile']}}@endif</td>
                        <td>@if($value['gender'] == 1) 女 @elseif($value['gender'] == 2) 男 @else 未知 @endif</td>
                        <td>{{$value['age']}}</td>
                        <td>{{$value['beauty']}}</td>
                        <td>{{$value['expression']}}</td>
                        <td>@if($value['hat'] == 1) 有 @elseif($value['hat'] == 0) 无 @else 未知 @endif</td>
                        <td>@if($value['glass'] == 1) 有 @elseif($value['glass'] == 0) 无 @else 未知 @endif</td>
                        <td>@if($value['mask'] == 1) 有 @elseif($value['mask'] == 0) 无 @else 未知 @endif</td>
                        <td>@if($value['hair-length'] == 1) 短发 @elseif($value['hair-length'] == 2) 中发 @elseif($value['hair-length'] == 3) 长发 @elseif($value['hair-length'] == 4) 绑发 @elseif($value['hair-length'] == 0) 光头 @else 未知 @endif</td>
                        <td>@if($value['hair-bang'] == 1) 有 @elseif($value['hair-bang'] == 0) 无 @else 未知 @endif</td>
                        <td>@if($value['hair-color'] == 1) 金色 @elseif($value['hair-color'] == 2) 棕色 @elseif($value['hair-color'] == 3) 灰白色 @elseif($value['hair-color'] == 0) 黑色 @else 未知 @endif</td>
                        <td>{{$value['cost']}} 积分</td>
                        <td>{{$value['gain']}} 积分</td>
                        <td>{{$value['created_at']}}</td>
                        <td>
                            <a class='btn btn-default' href="{{ yzWebUrl('plugin.face-analysis.admin.face-analysis-log-manage.del', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"><i class="fa fa-remove"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $pager !!}
    </div>
</div>
@endsection

