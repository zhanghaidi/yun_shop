@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">

            <div class='panel-heading'>
                分享设置
            </div>
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="share[title]" class="form-control" value="{{ $set['title'] }}" />
                        <span class="help-block">不填写默认商城名称</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图标</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('share[icon]', $set['icon'])!!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea style="height:100px;" name="share[desc]" class="form-control" cols="60">{{ $set['desc'] }}</textarea>
                    </div>
                </div>

                       <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"  />
                     </div>
            </div>

            </div>
        </div>
    </form>
</div>
</div>

{{--点击复制链接--}}
    <script>
        $('#cp').click(function () {
            util.clip(this, $(this).attr('data-url'));
        });
    </script>
@endsection
