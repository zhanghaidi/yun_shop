@extends('layouts.base')
@section('title', '弹窗位置')
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('plugin.min-app.Backend.Controllers.popup.position')}}">弹窗位置列表</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;弹窗位置</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="{{yzWebUrl('plugin.min-app.Backend.Controllers.popup.position-edit',['id'=>$position['id']])}}" method='post' class='form-horizontal'>
                <input type="hidden" name="op" value="position-edit">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="popup"/>
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">弹窗位置名称</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="position[position_name]" class="form-control" value="{{$position['position_name']}}" placeholder="请弹窗位置名称" /></div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >小程序账号ID</label>
                            <div class="col-sm-9 col-xs-12">
                                <select name="position[weapp_account_id]" class="form-control">
                                    @foreach(Illuminate\Support\Facades\DB::table('account_wxapp')->select('uniacid','name')->orderBy('uniacid','desc')->get() as $item)
                                        <option @if((empty($position['weapp_account_id']) && $item['uniacid'] == 45) || (!empty($position['weapp_account_id']) && $position['weapp_account_id'] == $item['uniacid'])) selected @endif value="{{$item['uniacid']}}">{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

{{--                        <div class="form-group">--}}
{{--                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">位置类型</label>--}}
{{--                            <div class="col-sm-9 col-xs-12">--}}
{{--                                @foreach($pos_type as $key => $item)--}}
{{--                                <label class='radio-inline'><input type='radio' name='position[type]' value='{{$key}}' @if ($position['type'] == $key) checked @endif/>{{$item}}</label>--}}
{{--                                @endforeach--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'><input type='radio' name='position[is_show]' value='1' @if (!$position['id'] || $position['is_show'] == 1) checked @endif/>开启</label>
                                <label class='radio-inline'><input type='radio' name='position[is_show]' value='0' @if ($position['id'] && $position['is_show'] == 0) checked @endif />关闭</label>
                            </div>
                        </div>

                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit"  name="submit" value="提交" class="btn btn-success"/>
                                <input type="hidden" name="position[id]" value="{{$position['id']}}"/>
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
