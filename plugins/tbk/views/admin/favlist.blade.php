@extends('layouts.base')

@section('content')
@section('title', '自选库管理')
<div class="page-heading">
    <h2>自选库管理</h2>
</div>
<div class="panel panel-info">
    {{--<div class="panel-body">
        <form action="" method="post" class="form-horizontal" role="form" id="form1">
            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <div class="">
                    <input type="text" class="form-control"  name="kwd" value="{{$kwd?$kwd:''}}" placeholder="关键字"/>
                </div>
            </div>
            <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                <div class="">
                    <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                </div>
            </div>
        </form>
    </div>--}}

    @if ($favlist->total_results > 0)
        <table class="table table-responsive table-hover">
            <thead>
            <tr>
                <th style="width:50px;text-align: center;">选品库id</th>
                <th style="width:50px;text-align: center;">选品组名称</th>
                <th style="width:50px;text-align: center;">选品库类型</th>
                <th style="width:50px;text-align: center;">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($favlist->results->tbk_favorites as $row)
                <tr>
                    <td style="text-align: center;">
                        {{$row->favorites_id}}
                    </td>
                    <td style="text-align: center;">
                        {{$row->favorites_title}}
                    </td>
                    <td style="text-align: center;">
                        @if ($row->type == 1) 普通类型 @else 高佣金类型 @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="javascript:void(0)" favorites-id="{{$row->favorites_id}}" onClick="addImport({{$row->favorites_id}})" >导入到商城</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class='panel panel-default'>
            <div class='panel-body' style='text-align: center;padding:30px;'>
                暂时没有任何选品库!
            </div>
        </div>
    @endif

</div>

<script >
    function addImport(id){
        $.ajax({
            type:'get',
            url:"{!!yzWebUrl('plugin.tbk.admin.selection.favourite')!!}",
            data:{id:id},
            success:function(res){
                console.log(res)
                if(res.result==1){
                    alert('加入队列成功');
                }
            }
        })
    }
</script>
@endsection

