@extends('layouts.base')

@section('content')
@section('title', trans('商品简称'))
<div class="w1200 m0a">
    <div class="main rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">关键字</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="search[keyword]" id="" type="text" value="@if(!empty($search['keyword'])){{$search['keyword']}}@endif">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <select name="search[status]" class='form-control'>
                                <option value="1" @if($search['status'] != 0) selected @endif>上架</option>
                                <option value="0" @if($search['status'] == 0) selected @endif>下架</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类</label>
                        <div class="col-sm-8 col-xs-12">
                            {!!$catetory_menus!!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">简称状态</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <select name="search[short]" class='form-control'>
                                <option value="1" @if($search['short'] == 1) selected @endif>已填写</option>
                                <option value="0" @if($search['short'] == 0) selected @endif>未填写</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-8 col-xs-12">
                            <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <form action="{{yzWebUrl('plugin.exhelper.admin.short.edit')}}" method="post">
            <div class="panel panel-default">
                <div class="panel-body table-responsive">
                    <table class="table table-hover">
                        <thead class="navbar-inner">
                        <tr>
                            <th style="width:60px;">ID</th>
                            <th style='width:350px;'>商品</th>
                            <th>商品简称</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td title="{!! $item->title !!}">
                                <img src='{!! tomedia($item->thumb) !!}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/> {{$item->title}}</td>
                            <td>
                                <input type="text" class="form-control" name="short_title[{{$item->id}}]" value="@if(!is_null($item->hasOneShort)){{$item->hasOneShort->short_title}}@endif">
                            </td>

                        </tr>
                        @endforeach
                        <tr>
                            <td colspan='3'>
                                <input name="submit" type="submit" class="btn btn-primary" value="批量修改商品简称">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
        </form>
    </div>
</div>
@endsection('content')
