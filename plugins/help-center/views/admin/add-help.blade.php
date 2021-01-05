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

                    <div><b>添加帮助:</b></div><hr>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">帮助标题</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="adddata[title]" value="{{ $data->title }}" placeholder="建议输入4个字标题">
                            </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">排序</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="adddata[sort]" value="{{ $data->sort ? $data->sort: 0 }}" placeholder="排序数值小,显示在前面">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6" style="margin-left: 16%;">
                            <textarea class="form-control" name="adddata[content]" rows="5">{{ $data->content }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <input type="submit" name="submit" value="保存提交" class="btn btn-default"/>
                                <a class="btn btn-default" href="{{ $backurl }}" role="button">返回列表</a>
                            </div>
                    </div>

            </div>
        </form>
    </section><!-- /.content -->
@endsection

