@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

    {{--<script type="text/javascript">--}}
        {{--window.optionchanged = false;--}}
        {{--require(['bootstrap'], function () {--}}
            {{--$('#myTab a').click(function (e) {--}}
                {{--e.preventDefault();--}}
                {{--$(this).tab('show');--}}
            {{--})--}}
        {{--});--}}
    {{--</script>--}}

    <section class="content">
        <form id="setform" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel-body">

                    <div><b>基础设置:</b></div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $pluginName }}启用</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[status]" value="1" @if($set['status'] == 1) checked="checked" @endif /> 启用
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[status]" value="0" @if($set['status'] == 0) checked="checked" @endif /> 禁用
                            </label>
                        </div>
                    </div>

                    <div><b>分享设置:</b></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">分享标题</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" name="setdata[title]" value="{{ $share_data['title'] }}" placeholder="不填写默认商城名称"/>
                            </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">分享图标</label>
                            <div class="col-sm-7">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[icon]', $share_data['icon']) !!}
                            </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">分享描述</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="setdata[description]" rows="10"  placeholder="请输入具体描述........">{{ $share_data['description'] }}</textarea>
                            </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-2">
                                <input type="submit" name="submit" value="提交" class="btn btn-success" onclick="return formcheck()"/>
                            </div>
                    </div>
            </div>
        </form>
    </section><!-- /.content -->
@endsection

