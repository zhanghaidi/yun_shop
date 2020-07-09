@extends('layouts.base')
@section('title', trans('保险公司列表'))
@section('content')
    <div class="w1200 ">
        <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">

        <div id="goods-index" class=" rightlist ">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">保险公司</a></li>
                </ul>
            </div>

            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="supplier_order" id="form_do"/>
                        <input type="hidden" name="route" value="plugin.supplier.admin.controllers.insurance.insurance.company-list" id="form_p"/>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-8">
                            <div class="">
                                <input type="text" class="form-control"  name="search[name]" value="{{$search['name']?$search['name']:''}}" placeholder="保险公司名称"/>
                            </div>
                        </div>

                        <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                            <div class="">
                                <button class="btn btn-success "><i class="fa fa-search" onclick="search()"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->
                <form id="goods-list" action="{!! yzWebUrl($sort_url) !!}" method="post">
                    <div class="panel panel-default">
                        <div class="panel-heading">总数：{{$list['total']}}   </div>
                        <div class="panel-body table-responsive">
                            <table class="table table-hover">
                                <thead class="navbar-inner">
                                <tr>
                                    <th width="auto">ID</th>
                                    <th width="auto">排序</th>
                                    <th width="auto">保险公司名称</th>
                                    <th width="auto">是否显示</th>
                                    <th width="auto">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list['data'] as $item)
                                 <tr>
                                     <td>
                                         {{ $item['id'] }}
                                     </td>
                                     <td>
                                         <input type="text" style="width: 100px" class="form-control"
                                                name="display_order[{{$item['id']}}]"
                                                value="{{$item['sort']}}">
                                     </td>
                                     <td>{{$item['name']}}</td>
                                     <td>
                                         <input class="mui-switch mui-switch-animbg" id="show_status_{!! $item['id'] !!}" type="checkbox"
                                                @if($item['is_show'])
                                                checked
                                                @endif
                                                onclick="message_default(this.id, {{$item['id']}})"/>
                                     </td>
                                     <td>
                                         <a class="btn btn-default" href="{!! yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.company-edit', ['id'=>$item['id']]) !!}">编辑</a>
                                         <a class="btn btn-default" href="{!! yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.company-del', ['id'=>$item['id']]) !!}" onclick="return confirm('是否删除');return false;">删除</a>
                                     </td>
                                 </tr>
                                @endforeach
                                </tbody>
                            </table>

                        {!!$paper!!}
                        <!--分页-->
                        </div>
                        <div class='panel-footer'>
                            @section('add_company')
                                <a class='btn btn-success ' href="{{yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.company-add')}}"><i
                                            class='fa fa-plus'></i> 添加保险公司</a>
                            @show
                            @section('sub_sort')
                                <input name="submit" type="submit" class="btn btn-default back" value="提交排序">
                            @show
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <script language='javascript'>
        function search() {
            $("input[name^='route']").val('plugin.supplier.admin.controllers.insurance.insurance.company-list');
        }
        function message_default(name, s_id) {
            var id = "#" + name;
            var url_open = "{!! yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.change-open') !!}"
            var url_close = "{!! yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.change-close') !!}"
            var postdata = {
                id: s_id,
            };
            if ($(id).is(':checked')) {
                //开
                $.post(url_open, postdata, function(data){
                    if (data.result == 1) {
                        alert(data.msg)
                    } else {
                        alert(data.msg)
                    }
                }, "json");
            } else {
                //关
                $.post(url_close, postdata, function(data){
                    alert(data.msg)
                }, "json");
            }
        }
    </script>
@endsection