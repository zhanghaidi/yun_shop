@extends('layouts.base')

@section('content')
@section('title', trans('风控商品列表'))

<div class="w1200 ">
    <div class=" rightlist ">

        <div class="right-titpos">
            <ul class="add-snav">
                风控商品列表
                <button class='btn btn-primary' id="add_goods"
                   style="margin-bottom:5px;"><i class='fa fa-plus'></i> 添加商品</button>
            </ul>
        </div>
        <div class="clearfix">
            <div class='panel panel-default'>
                <div class='panel-body  table-responsive'>
                    <table class="table table-hover" style="overflow:visible;">
                        <thead>
                        <tr>
                            <th style='width:8%;'>ID</th>
                            <th style='width:15%;'>商品</th>
                            <th style='width:30%;'>商品名称</th>
                            <th style='width:12%;'>价格<br>库存</th>
                            <th style='width:10%;'>销量</th>
                            <th style='width:10%;'>状态</th>
                            <th style='width:10%;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $row)
                            <tr>
                                <td>{{$row['id']}}</td>
                                <td>
                                    <img src="{{tomedia($row['thumb'])}}"
                                         style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                    </br>
                                </td>
                                <td>{{$row['title']}}</td>
                                <td>{{$row['price']}}<br>{{$row['stock']}}</td>
                                <td>{{$row['real_sales']}}</td>
                                <td>
                                    @if($row['status'] == 1)
                                        <label class="label label-default">上架</label>
                                    @else
                                        <label class="label label-default">下架</label>
                                    @endif
                                </td>
                                <td> <a href="{{yzWebUrl('plugin.jd-supply.admin.goods-control.delete',['id'=>$row['id']])}}"
                                        onclick="return confirm('是否确认删除?');
			                                                   return false;" class="btn btn-default  btn-sm"
                                        title="删除"><i
                                                class="fa fa-trash"></i></a></td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>

                    {!! $pager !!}
                </div>
            </div>
        </div>





    </div>
</div>
<div id="modal-module-menus-goods" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{yzWebUrl('plugin.jd-supply.admin.goods-control.add')}}" method="post">
        <div class="modal-content">
            <div class="modal-header" style = "text-align: center">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h7 >搜索商品</h7></div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods" placeholder="请输入商品ID/关键字"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default"  onclick="search_goods();">搜索</button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-goods"></div>
            </div>
            <div class="modal-footer">
{{--                <a href="#" onclick="select_goods()" class="btn btn-default" data-dismiss="modal" aria-hidden="true" >确认</a>--}}
                <button type="submit" class="btn btn-default">确定</button>
                <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
        </form>

    </div>
</div>
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
<script language='javascript'>
    $(document).on('click', '#add_goods', function() {
        $('#modal-module-menus-goods').modal();
    });
    //选择直播间
    function search_goods(o) {
        if ($('#search-kwd-goods').val() == '') {
            Tip.focus('#search-kwd-goods', '请输入关键词');
            return;
        }
        $("#module-menus-goods").html("正在搜索....");
        $.get("{!! yzWebUrl('plugin.jd-supply.admin.goods-control.goods-search') !!}", {
            keyword: $.trim($('#search-kwd-goods').val()),
        }, function (dat) {
            $('#module-menus-goods').html(dat);
        });
    }

</script>
@endsection