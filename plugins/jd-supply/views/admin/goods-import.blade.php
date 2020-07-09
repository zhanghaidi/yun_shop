@extends('layouts.base')

@section('content')
@section('title', trans('商品列表'))
    <div class="w1200 ">


        <script type="text/javascript" src="/static/resource/js/lib/jquery-ui-1.10.3.min.js"></script>
        <script src="https://cdn.static.runoob.com/libs/angular.js/1.4.6/angular.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
        <div id="goods-index" class=" rightlist ">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">商品列表</a></li>
                </ul>
            </div>
            <div class="right-addbox">
                {{--<div class="panel panel-info">--}}
                    {{--<div class="panel-body">--}}
                        {{--<form action="" method="post" class="form-horizontal" role="form">--}}
                            {{--<div class="form-group col-xs-12 col-sm-8 col-lg-2">--}}
                                {{--<div class="">--}}
                                    {{--<input class="form-control" placeholder="请输入关键字" name="search[keyword]" id=""--}}
                                           {{--type="text" value="{{$requestSearch['keyword']}}" ／>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="form-group col-xs-12 col-sm-8 col-lg-2">--}}
                                {{--<div class="">--}}
                                    {{--<select name="search[status]" class='form-control'>--}}
                                        {{--<option value="">状态不限</option>--}}
                                        {{--<option value="1"--}}
                                                {{--@if($requestSearch['status'] == '1') selected @endif>上架</option>--}}
                                        {{--<option value="0"--}}
                                                {{--@if($requestSearch['status'] == '0') selected @endif>下架</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class='form-input col-xs-12 col-sm-8 col-lg-6'>--}}
                                {{--<p class="input-group-addon price">价格区间</p>--}}
                                {{--<input class="form-control price" name="search[min_price]" id="minprice" type="text"--}}
                                       {{--value="{{$requestSearch['min_price']}}" ／>--}}
                                {{--<p class="line">—</p>--}}
                                {{--<input class="form-control price" name="search[max_price]" id="max_price"--}}
                                       {{--type="text" value="{{$requestSearch['min_price']}}"／>--}}
                            {{--</div>--}}
                            {{--<div class="form-group col-xs-8 col-sm-8 col-lg-1">--}}
                                {{--<button class="btn btn-block btn-success"><i class="fa fa-search"></i> 搜索</button>--}}
                            {{--</div>--}}
                        {{--</form>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div ng-app="">
                <form id="goods-list" action="{!! yzWebUrl('plugin.jd-supply.admin.goods-import.select') !!}" method="post">

                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="form-group col-xs-12 col-sm-8 col-lg-5">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">导入分类</label>
                                <div class="col-sm-12 col-xs-12">
                                    {!!$catetory_menus!!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead class="navbar-inner">
                                <tr>
                                    <td style='width:4%;text-align: center;'>
                                        <input type="checkbox" ng-model="all">
                                    </td>
                                    <th style='width:6%;text-align: center;'>ID</th>
                                    <th style='width:6%;text-align: center;'>商品</th>
                                    <th style='width:26%;text-align: center;'>商品名称</th>
                                    <th style='width:16%;text-align: center;'>京东价格<br/>库存</th>
                                    <th style='width:10%;text-align: center;'>销量</th>
                                    {{--<th style='width:10%;text-align: center;'>状态</th>--}}
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($list['data']['list'] as $item)
                                <tr>
                                    <td style='text-align: center;'>
                                        <input type="checkbox" ng-checked="all" name="goods_ids[]" value="{{$item['goods_id']}}">
                                    </td>
                                    <td style='text-align: center;'>{{$item['goods_id']}}</td>
                                    <td style='text-align: center;' title="{{$item['goods_title']}}">
                                        <img src="{{$item['default_image']}}"
                                             style="width:40px;height:40px;padding:1px;border:1px solid #ccc;"/>
                                    </td>
                                    <td style='text-align: center;'>{{$item['goods_title']}}</td>
                                    <td style='text-align: center;'>{{$item['Price']}}<br/>{{$item['goodsNowStock']}}</td>
                                    <td  style='text-align: center;'>{{$item['real_sale']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$pager!!}
                    </div>
                    <div class='panel-footer'>
                        <input name="submit" type="submit" class="btn btn-success" value="导入商品">
                    </div>
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection('content')