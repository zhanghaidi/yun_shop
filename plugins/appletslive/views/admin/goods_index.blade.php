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
                <input type="hidden" name="type" value="1"/>
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
                <a id="btn-room-refresh" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;" @click="refresh" v-if="allowRefresh==1">同步商品列表</a>
                <a id="btn-room-refresh" class="btn btn-defaultt disabled" style="height: 35px;margin-top: 5px;color: white;"
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
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.goods.resetaudit', ['id' => $row['id']])}}"
                               title='撤回提审'>撤回提审
                            </a>
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.goods.audit', ['id' => $row['id']])}}"
                               title='提审'>提审
                            </a>

                            @if ($row['audit_status'] != 1)
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.edit', ['id' => $row['id']])}}"
                                   title='录播列表'><i class='fa fa-list'></i>更新商品
                                </a>
                            @endif

                            <a class='btn btn-danger'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.del', ['id' => $row['id']])}}"
                               title='录播列表'>删除商品
                            </a>
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
                    this.$http.get("{!! yzWebUrl('plugin.appletslive.admin.controllers.goods.index', ['tag'=>'refresh']) !!}")
                        .then(res => {
                            that.allowRefresh = 1;
                            this.$message({
                                type: 'success',
                                duration: 1000,
                                message: res.data.msg,
                                onClose: function () {
                                    {{--location.href = "{!! yzWebUrl('plugin.appletslive.admin.controllers.goods.index') !!}";--}}
                                }
                            });
                        });
                }
            }
        });
    </script>
@endsection