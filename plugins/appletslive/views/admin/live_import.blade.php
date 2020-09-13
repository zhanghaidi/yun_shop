@extends('layouts.base')
@section('title', trans('导入商品'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">导入商品</a></li>
        </ul>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <form action="" method="get" class="form-horizontal" role="form" id="form1">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                <input type="hidden" name="id" value="{{ $request['id'] }}"/>
                <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.live.import"/>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                        <input type="text" class="form-control" name="search[name]"
                               value="{{$request['search']['name']}}" placeholder="商品名称"/>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class='panel panel-default'>
        <div class='panel-body'>
            <div class="clearfix panel-heading" id="goodsTable">
                <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.live.index')}}">返回</a>
                <a id="btnImport" class="btn btn-primary" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;">导入</a>
            </div>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr style="">
                    <th style='width:10%;text-align:center;'>
                        <label for="checkall" class="checkbox-inline" style="margin-bottom:20px;">
                            <input type="checkbox" id="checkall" />
                        </label>
                    </th>
                    <th style='width:10%;'>ID</th>
                    <th style='width:15%;'>封面</th>
                    <th style='width:25%;'>名称</th>
                    <th style='width:15%;'>价格(元)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($goods as $row)
                    <tr style="">
                        <td style="text-align:center;">
                            <label for="checkitem_{{ $row['id'] }}" class="checkbox-inline" style="margin-bottom:20px;">
                                <input type="checkbox" class="checkitem" id="checkitem_{{ $row['id'] }}" value="{{ $row['id'] }}" />
                            </label>
                        </td>
                        <td>{{ $row['id'] }}</td>
                        <td>
                            <img src="{!! tomedia($row['cover_img_url']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </td>
                        <td>{{ $row['name'] }}</td>
                        <td>
                            @if ($row['price_type'] == 1)
                                {{ floatval($row['price']) }}
                            @elseif ($row['price_type'] == 2)
                                {{ floatval($row['price']) }} ~ {{ floatval($row['price2']) }}
                            @else
                                原价：{{ floatval($row['price']) }} 现价：{{ floatval($row['price2']) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="width:100%;height:150px;"></div>

    <script>
        $('#checkall').on('click', function () {
            console.log('checkall', $(this).prop('checked'));
            if ($(this).prop('checked')) {
                $('.checkitem').each(function () {
                    $(this).prop('checked', 'checked');
                });
            } else {
                $('.checkitem').each(function () {
                    $(this).prop('checked', false);
                });
            }
        });

        $('#btnImport').on('click', function () {
            var ids = [];
            $('.checkitem').each(function () {
                if ($(this).prop('checked')) {
                    ids.push($(this).val());
                }
            });

            if (ids.length == 0) {
                util.message('请勾选需要导入的商品', '', 'info');
            } else {
                $('#btnImport').button('loading');
                $.ajax({
                    url: "",
                    type: 'POST',
                    data: {id: "{{ $id }}", goods_ids: ids.join(',')},
                    success: function (res) {
                        $('#btnImport').button('reset');
                        var jump = "{!! yzWebUrl('plugin.appletslive.admin.controllers.live.index') !!}";
                        util.message(res.msg, res.result == 1 ? jump : '', res.result == 1 ? 'success' : 'info');
                    }
                });
            }
        });
    </script>

@endsection