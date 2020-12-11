@extends('layouts.base')

@section('content')
@section('title', trans('自定义表单管理'))
<link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
@if(config('APP_Framework') == 'platform')
    <script type="text/javascript" src="/static/resource/js/lib/jquery-ui-1.10.3.min.js"></script>
@else
    <script type="text/javascript" src="/addons/yun_shop/static/resource/js/lib/jquery-ui-1.10.3.min.js"></script>
@endif

<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">自定义表单管理</a></li>
        <a class='btn btn-primary' href="{{yzWebUrl('plugin.diyform.admin.diyform.add-form')}}"
           style="margin-bottom:5px;"><i class='fa fa-plus'></i> 添加表单</a>
    </ul>
</div>


<div class='panel panel-default'>

    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:5%;'>ID</th>
                <th style='width:10%;'>表单名称</th>
                <th style='width:5%;text-align: center;'>小程序</th>
                <th style='width:10%;'>操作</th>
            </tr>
            </thead>
            <tbody>

            @foreach($list['data'] as $row)
                <tr>
                    <td>{{$row['id']}}</td>
                    <td>{{$row['title']}}</td>
                    <td style="text-align: center;position:relative; overflow:visible;" width="20%">
                        {{--<a class="btn btn-sm btn-default umphp" title="二维码">--}}
                            {{--<div class="img">--}}
                                {{--<img style="width: 120px;high:120px;" src="{{$row['qrcode_img']}}">--}}
                            {{--</div>--}}
                            {{--<i class="fa fa-qrcode"></i>--}}
                        {{--</a>--}}
                        <a href="javascript:;"
                           data-clipboard-text="{{'/pages/shopping/goods-form/index?form_id='.$row['id']}}"
                           data-url="{{'/pages/shopping/goods-form/index?form_id='.$row['id']}}"
                           title="复制小程序链接" class="btn btn-default btn-sm js-clip"><i class="fa fa-link"></i>
                        </a>
                    </td>
                    <td>

                        <a href="javascript:;"
                           data-clipboard-text="{{yzAppFullUrl('diyform/'.$row['id'])}}"
                               data-url="{{yzAppFullUrl('diyform/'.$row['id'])}}"
                           title="复制链接" class="btn btn-default btn-sm js-clip"><i class="fa fa-link"></i>
                        </a>
                        <a class='btn btn-default'
                           href="{{yzWebUrl('plugin.diyform.admin.diyform.edit-form',['id'=>$row['id']])}}"><i
                                    class='fa fa-edit'></i> 编辑</a>
                        <a class='btn btn-default'
                           href="{{yzWebUrl('plugin.diyform.admin.diyform-data.get-form-data',['id'=>$row['id']])}}"><i
                                    class='fa fa-edit'></i> 查看</a>
                        <a class='btn btn-default btn-sm'
                           href="{{yzWebUrl('plugin.diyform.admin.diyform.del-form',['id'=>$row['id']])}}"
                           onclick="return confirm('确认删除此表单吗？');return false;"><i class='fa fa-remove'></i> 删除</a>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>
<script>
    $('.umphp').hover(function () {
            var url = $(this).attr('data-url');
            $(this).addClass("selected");
        },
        function () {
            $(this).removeClass("selected");
        }
    );
</script>
@endsection