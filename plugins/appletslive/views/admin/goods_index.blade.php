@extends('layouts.base')
@section('title', trans('商品管理'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">商品管理</a></li>
        </ul>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <form action="" method="get" class="form-horizontal" role="form" id="form2">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.goods.index"/>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <input type="number" placeholder="购物袋商品ID" class="form-control" name="search[id]"
                               value="{{$request['search']['id']}}"/>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                        <input type="text" class="form-control" name="search[name]"
                               value="{{$request['search']['name']}}" placeholder="商品名称"/>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <select name="search[audit_status]" class="form-control">
                            <option value="">请选择审核状态</option>
                            <option value="0" @if($request['search']['audit_status']=='0') selected @endif>未审核</option>
                            <option value='1' @if($request['search']['audit_status']=='1') selected @endif>审核中</option>
                            <option value='2' @if($request['search']['audit_status']=='2') selected @endif>审核通过</option>
                            <option value='3' @if($request['search']['audit_status']=='3') selected @endif>审核失败</option>
                        </select>
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
                <a class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;" @click="refresh" v-if="allowRefresh==1">同步商品列表</a>
                <a class="btn btn-defaultt disabled" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;" disabled v-else>同步商品列表</a>
                <a id="" class="btn btn-primary" style="height: 35px;margin-top: 5px;color: white;"
                   href="{{ yzWebUrl('plugin.appletslive.admin.controllers.goods.add') }}">添加商品</a>
            </div>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr>
                    <th style='width:10%;'>ID</th>
                    <th style='width:15%;'>封面</th>
                    <th style='width:25%;'>名称</th>
                    <th style='width:15%;'>价格(元)</th>
                    <th style='width:15%;'>审核状态</th>
                    <th style='width:20%;'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
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
                            @if ($audit_status[$row['id']] == 0)
                                未审核
                                @if($row['reset_audit'] == 1)
                                    (已撤回)
                                @endif
                            @elseif ($audit_status[$row['id']] == 1)
                                审核中
                            @elseif ($audit_status[$row['id']] == 2)
                                审核通过
                            @elseif ($audit_status[$row['id']] == 3)
                                审核失败
                            @else
                                未知
                            @endif
                        </td>
                        <td style="overflow:visible;">
                            @if ($audit_status[$row['id']] == 1)
                                <button class='btn btn-default btn-reset-audit'
                                   data-href="{{yzWebUrl('plugin.appletslive.admin.controllers.goods.resetaudit', ['id' => $row['id']])}}"
                                   title='撤回提审' data-toggle="modal" data-target="#modal-reset-audit-warning">撤回提审
                                </button>
                            @endif

                            @if ($audit_status[$row['id']] == 0)
                                <button class='btn btn-default btn-audit'
                                   data-href="{{yzWebUrl('plugin.appletslive.admin.controllers.goods.audit', ['id' => $row['id']])}}"
                                   title='重新提审' data-toggle="modal" data-target="#modal-audit-warning">重新提审
                                </button>
                            @endif

                            @if ($audit_status[$row['id']] == 0 || $audit_status[$row['id']] == 2)
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.goods.edit', ['id' => $row['id']])}}"
                                   title='更新商品'><i class='fa fa-list'></i>更新商品
                                </a>
                            @endif

                            @if ($audit_status[$row['id']] == 0 || $audit_status[$row['id']] == 2 || $audit_status[$row['id']] == 3)
                                <button class='btn btn-danger btn-delete'
                                   data-url="{{yzWebUrl('plugin.appletslive.admin.controllers.goods.del', ['id' => $row['id']])}}"
                                   title='删除商品' data-toggle="modal" data-target="#modal-delete-warning">删除商品
                                </button>
                            @endif
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

        // 同步商品
        var app = new Vue({
            el: '#goodsTable',
            data: {
                allowRefresh: 1
            },
            mounted: function () {
            },
            methods: {
                refresh() {
                    var that = this;
                    that.allowRefresh = 0;
                    this.$http.get("{!! yzWebUrl('plugin.appletslive.admin.controllers.goods.index', ['tag' => 'refresh']) !!}")
                        .then(res => {
                            that.allowRefresh = 1;
                            this.$message({
                                type: 'success',
                                duration: 1000,
                                message: res.data.msg,
                                onClose: function () {
                                    location.href = "{!! yzWebUrl('plugin.appletslive.admin.controllers.goods.index') !!}";
                                }
                            });
                        });
                }
            }
        });

        var Page = {
            init: function () {
                var that = this;

                // 查看商品封面大图
                $('.show-cover-img-url-big').on('mouseover', function () {
                    $(this).find('.img-big').show();
                });
                $('.show-cover-img-url-big').on('mouseout', function () {
                    $(this).find('.img-big').hide();
                });

                // 监听撤销审核按钮事件
                $(document).on('click', '.btn-reset-audit', function () {
                    var sureResetAuditGoods = document.getElementById('sureResetAuditGoods');
                    sureResetAuditGoods.dataset.href = $(this).data('href');
                });
                $(document).on('click', '#sureResetAuditGoods', function () {
                    var sureResetAuditGoods = document.getElementById('sureResetAuditGoods');
                    $(sureResetAuditGoods).button('loading');
                    location.href = sureResetAuditGoods.dataset.href;
                });

                // 监听重新提审按钮事件
                $(document).on('click', '.btn-audit', function () {
                    var sureAuditGoods = document.getElementById('sureAuditGoods');
                    sureAuditGoods.dataset.href = $(this).data('href');
                });
                $(document).on('click', '#sureAuditGoods', function () {
                    var sureAuditGoods = document.getElementById('sureAuditGoods');
                    $(sureAuditGoods).button('loading');
                    location.href = sureAuditGoods.dataset.href;
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
                            var jump = "{!! yzWebUrl('plugin.appletslive.admin.controllers.goods.index') !!}";
                            util.message(res.msg, res.result == 1 ? jump : '', res.result == 1 ? 'success' : 'info');
                        }
                    }
                });
            }
        };

        Page.init();

    </script>
@endsection