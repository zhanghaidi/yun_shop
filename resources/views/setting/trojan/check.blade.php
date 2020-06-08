@extends('layouts.base')
@section('title', trans('检查木马文件'))

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">

            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">检查木马文件</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="" method="get" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="form-group">
                        <div class="col-sm-2 col-xs-12">
                            <input type="button" id="btn_chk"  name="btn_chk" value="立即检查" class="btn btn-success"/>
                            @if (count($files) > 0)
                            <input type="button" id="btn_del"  name="btn_del" value="删除文件" class="btn btn-warning"/>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="col-sm-9 col-xs-9">
                    @if ($show)
                    @if (count($files) > 0)
                    <div class="col-sm-12 col-xs-12">
                        <div class="alert alert-danger" role="alert">可疑文件 ({{count($files)}})</div>
                        <ul class="list-group">
                       @foreach ($files as $rows)
                                <li class="list-group-item">{{$rows}}</li>
                       @endforeach
                        </ul>
                    </div>
                    @else
                    <div class="alert alert-info" role="alert">无可疑文件</div>
                    @endif
                    @endif
                    </div>
                </div>

                <script language="javascript">
                    var del_file = "{{$del_file}}"
                    $(function () {
                       $("#btn_chk").click(function () {
                           location.href = "{!! yzWebUrl('setting.trojan.check') !!}" + '&trojan=check';
                       });

                       $("#btn_del").click(function () {
                           $.post('{!! yzWebUrl('setting.trojan.del') !!}', {
                                   files: del_file
                               }, function (json) {
                               var obj = $.parseJSON(json);
                               if (obj.status == 1) {
                                   alert('删除成功');
                                   location.href = "{!! yzWebUrl('setting.trojan.check') !!}" + '&trojan=check';
                               } else if (obj.status == 2){
                                   alert('删除失败');
                               }
                               }
                           );
                       });
                    })
                </script>
            </form>
        </div>
    </div>
@endsection