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
                <a id="btnBatchImport" class="btn btn-primary" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;">批量导入</a>
                <a id="btnBatchRemove" class="btn btn-danger" style="height:35px;margin-top:5px;color:white;display:none;"
                   href="javascript:;;">批量移除</a>
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
                    <th style='width:10%;'>导入状态</th>
                    <th style='width:20%;'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($goods as $row)
                    <tr style="">
                        <td style="text-align:center;">
                            @if(!in_array($row['id'], $goods_ids))
                                <label for="checkitem_{{ $row['id'] }}" class="checkbox-inline" style="margin-bottom:20px;">
                                    <input type="checkbox" class="checkitem" id="checkitem_{{ $row['id'] }}" value="{{ $row['id'] }}" />
                                </label>
                            @else
                                <label for="checkitem_{{ $row['id'] }}" class="checkbox-inline disabled" style="margin-bottom:20px;" disabled>
                                    <input type="checkbox" class="disabled" id="checkitem_{{ $row['id'] }}" value="{{ $row['id'] }}" disabled />
                                </label>
                            @endif
                        </td>
                        <td>{{ $row['id'] }}</td>
                        <td style="overflow:visible;">
                            <div class="show-cover-img-url-big" style="position:relative;width:50px;overflow:visible">
                                <img src="{!! tomedia($row['cover_img_url']) !!}" alt=""
                                     style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                <img class="img-big" src="{!! tomedia($row['cover_img_url']) !!}" alt=""
                                     style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                            </div>
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
                        <td>
                             @if(!in_array($row['id'], $goods_ids))
                                 <span class="text-default">未导入</span>
                            @else
                                 <span class="text-success">已导入</span>
                            @endif
                        </td>
                        <td>
                            @if(!in_array($row['id'], $goods_ids))
                                <a class="btn btn-primary btn-import" style="height: 35px;margin-top: 5px;color: white;"
                                   href="javascript:;;" data-id="{{ $row['id'] }}">导入</a>
                            @else
                                <a class="btn btn-danger btn-remove" style="height: 35px;margin-top: 5px;color: white;display: none;"
                                   href="javascript:;;" data-id="{{ $row['id'] }}">移除</a>
                            @endif
                            <button class='btn btn-danger btn-delete'
                                    data-url="{{yzWebUrl('plugin.appletslive.admin.controllers.goods.del', ['id' => $row['id']])}}"
                                    title='删除商品' data-toggle="modal" data-target="#modal-delete-warning">删除商品
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $pager !!}
        </div>
    </div>

    <div style="width:100%;height:150px;"></div>

    @include('Yunshop\Appletslive::admin.modals')

    <script>

        var Page = {
            data: {
                id: "{{ $id }}",
                goodsIds: JSON.parse('{!! json_encode($goods_ids) !!}'),
                checkedIds: []
            },
            init: function () {
                var that = this;

                // 查看商品封面大图
                $('.show-cover-img-url-big').on('mouseover', function () {
                    $(this).find('.img-big').show();
                });
                $('.show-cover-img-url-big').on('mouseout', function () {
                    $(this).find('.img-big').hide();
                });

                // 监听全选事件
                $('#checkall').on('click', function () {
                    that.data.checkedIds = [];
                    if ($(this).prop('checked')) {
                        $('.checkitem').each(function () {
                            $(this).prop('checked', 'checked');
                            that.data.checkedIds.push($(this).val());
                        });
                    } else {
                        $('.checkitem').each(function () {
                            $(this).prop('checked', false);
                        });
                    }
                });

                // 监听商品选中事件
                $('.checkitem').on('click', function () {
                    var val = $(this).val();
                    if ($(this).prop('checked')) {
                        that.data.checkedIds.push(val);
                    } else {
                        var idx = 0;
                        for (var i in that.data.checkedIds) {
                            if (that.data.checkedIds[i] === val) {
                                that.data.checkedIds.splice(i, 1);
                            }
                        }
                    }
                    if (that.data.checkedIds.length == $('.checkitem').length) {
                        $('#checkall').prop('checked', 'checked');
                    } else {
                        $('#checkall').prop('checked', false);
                    }
                });

                // 监听批量导入按钮事件
                $('#btnBatchImport').on('click', function () {
                    that.import();
                });

                // 监听批量移除按钮事件
                $('#btnBatchRemove').on('click', function () {
                    that.remove();
                });

                // 监听单个移除按钮事件
                $('.btn-import').on('click', function () {
                    that.import(false, this);
                });

                // 监听单个移除按钮事件
                $('.btn-remove').on('click', function () {
                    that.remove(false, this);
                });

                // 监听表格中删除按钮事件
                $(document).on('click', '.btn-delete', function () {
                    $('#modal-delete-warning').find('h3').html('确定删除吗');
                    $('#modal-delete-warning').find('.live-list').html('');
                    $('#modal-delete-warning').find('.modal-body').addClass('hide');

                    var btnSubmitDelGoods = document.getElementById('submitDelGoods');
                    var btnSubmitDelGoodsForce = document.getElementById('submitDelGoodsForce');
                    btnSubmitDelGoods.dataset.url = $(this).data('url');
                    btnSubmitDelGoodsForce.dataset.url = $(this).data('url');
                    $(btnSubmitDelGoods).removeClass('hide');
                    $(btnSubmitDelGoodsForce).addClass('hide');
                });

                // 监听模态框删除商品按钮事件
                $(document).on('click', '#submitDelGoods', function () {
                    var btnSubmitDelGoods = document.getElementById('submitDelGoods');
                    that.delete(btnSubmitDelGoods.dataset.url);
                });

                // 监听强制删除商品按钮事件
                $(document).on('click', '#submitDelGoodsForce', function () {
                    var btnSubmitDelGoodsForce = document.getElementById('submitDelGoodsForce');
                    that.delete(btnSubmitDelGoodsForce.dataset.url, true);
                });
            },
            import: function (isBatch = true, btn = null) {
                var that = this;
                if (isBatch && that.data.checkedIds.length == 0) {
                    util.message('请勾选需要导入的商品', '', 'info');
                } else {
                    var data = {};
                    if (isBatch) {
                        $('#btnBatchImport').button('loading');
                        data = {id: that.data.id, goods_ids: that.data.checkedIds.join(','), type: 'import'};
                    } else {
                        $(btn).button('loading');
                        data = {id: that.data.id, goods_ids: $(btn).data('id'), type: 'import'};
                    }
                    $.ajax({
                        url: "",
                        type: 'POST',
                        data: data,
                        success: function (res) {
                            if (isBatch) {
                                $('#btnBatchImport').button('reset');
                            } else {
                                $(btn).button('reset');
                            }
                            var jump = "{!! yzWebUrl('plugin.appletslive.admin.controllers.live.index') !!}";
                            util.message(res.msg, res.result == 1 ? jump : '', res.result == 1 ? 'success' : 'info');
                        }
                    });
                }
            },
            remove: function (isBatch = true, btn = null) {
                var that = this;
                if (isBatch && that.data.checkedIds.length == 0) {
                    util.message('请勾选需要移除的商品', '', 'info');
                } else {
                    var data = {};
                    if (isBatch) {
                        $('#btnBatchRemove').button('loading');
                        data = {id: that.data.id, goods_ids: that.data.checkedIds.join(','), type: 'remove'};
                    } else {
                        $(btn).button('loading');
                        data = {id: that.data.id, goods_ids: $(btn).data('id'), type: 'remove'};
                    }
                    $.ajax({
                        url: "",
                        type: 'POST',
                        data: data,
                        success: function (res) {
                            if (isBatch) {
                                $('#btnBatchRemove').button('reset');
                            } else {
                                $(btn).button('reset');
                            }
                            var jump = "{!! yzWebUrl('plugin.appletslive.admin.controllers.live.index') !!}";
                            util.message(res.msg, res.result == 1 ? jump : '', res.result == 1 ? 'success' : 'info');
                        }
                    });
                }
            },
            delete: function (url, force = false) {
                var btnId = 'submitDelGoods';
                if (force) {
                    btnId = 'submitDelGoodsForce';
                }
                $('#' + btnId).button('loading');

                $.ajax({
                    url: url,
                    type: 'get',
                    data: {force: force ? 1 : 0},
                    success: function (res) {
                        $('#' + btnId).button('reset');
                        if (res.result === 0 && res.data.inuse) {
                            var liveList = '';
                            for (var i = 0; i < res.data.inuse.length; i++) {
                                liveList += ''
                                    + '<div class="form-group">'
                                    + '    <label class="col-md-2 col-sm-3 col-xs-12 control-label">'
                                    + '    ' + res.data.inuse[i]['live_status_text'] + ':'
                                    + '    </label>'
                                    + '    <label class="col-md-10 col-sm-9 col-xs-12 control-label" style="font-weight: bold;text-align: left;">'
                                    + '    ' + res.data.inuse[i]['name']
                                    + '    </label>'
                                    + '</div>';
                            }
                            $('#modal-delete-warning').find('h3').html('以下直播间已导入该商品, 仍要删除吗');
                            $('#modal-delete-warning').find('.live-list').html(liveList);
                            $('#modal-delete-warning').find('.modal-body').removeClass('hide');
                            $('#submitDelGoods').addClass('hide');
                            $('#submitDelGoodsForce').removeClass('hide');
                        } else {
                            $('#modal-delete-warning').find('a').trigger('click');
                            util.message(res.msg, res.result == 1 ? location.href : '', res.result == 1 ? 'success' : 'info');
                        }
                    }
                });
            }
        };

        Page.init();

    </script>

@endsection