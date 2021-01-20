@extends('layouts.base')

@section('content')
@section('title', '打印机管理')
<div class="page-heading">
    <h2>小票打印机管理</h2>
</div>
<div class="panel panel-info">
    <div class="panel-body">
        <form action="" method="get" class="form-horizontal" role="form" id="form1">
            <input type="hidden" name="c" value="site"/>
            <input type="hidden" name="a" value="entry"/>
            <input type="hidden" name="m" value="yun_shop"/>
            <input type="hidden" name="do" value="printer" id="form_do"/>
            <input type="hidden" name="route" value="plugin.printer.admin.list.index" id="route" />
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
    </div>
    @if ($list->total() > 0)
        <table class="table table-responsive table-hover">
            <thead>
            <tr>
                <th style="width:10px;text-align: center;">ID</th>
                <th style="width:50px;text-align: center;">打印机名称</th>
                <th style="width:25px;text-align: center;">状态</th>
                <th style="width:50px;text-align: center;">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
            <tr>
                <td style="text-align: center;">
                    {{$row->id}}
                </td>
                <td style="text-align: center;">
                    {{$row->title}}
                </td>
                <td style="text-align: center;">
                    <label data='{{$row->status}}'
                           class='{{$row->status_obj['style']}}'
                           onclick="changeStatus(this, {{$row->id}}, 'status')">
                            {{$row->status_obj['name']}}
                    </label>
                </td>
                <td style="text-align: center;">
                    <a class='btn btn-default  btn-sm' href="{{yzWebUrl('plugin.printer.admin.list.edit', ['id' => $row->id])}}" >
                        <i class='fa fa-edit'></i>编辑
                    </a>
                    <a class='btn btn-default  btn-sm'  data-toggle='ajaxRemove' href="{{yzWebUrl('plugin.printer.admin.list.del', ['id' => $row->id])}}" data-confirm="确认删除此模板吗？" >
                        <i class='fa fa-trash'></i> 删除
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {!!$pager!!}
    @else
        <div class='panel panel-default'>
            <div class='panel-body' style='text-align: center;padding:30px;'>
                暂时没有任何小票打印机!
            </div>
        </div>
    @endif
    <div class='panel-footer'>
        <a class='btn btn-info' href="{{yzWebUrl('plugin.printer.admin.list.add')}}"><i class='fa fa-plus'></i> 添加打印机</a>
    </div>
</div>
<script type="text/javascript">
    function changeStatus(obj, id, type) {
        $(obj).html($(obj).html() + "...");
        $.post("{!! yzWebUrl('plugin.printer.admin.list.change-status') !!}", {id: id, type: type, data: obj.getAttribute("data")}
            , function (d) {
                $(obj).html($(obj).html().replace("...", ""));
                if (type == 'status') {
                    $(obj).html(d.data == '1' ? '开启' : '关闭');
                }
                $(obj).attr("data", d.data);
                if (d.result == 1) {
                    location.href = location.href;
                }
            }
            , "json"
        );
    }
</script>
@endsection