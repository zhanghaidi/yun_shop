@extends('layouts.base')
@section('title', '被收录情况')
@section('content')
    <div class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">被收录情况</a></li>
            </ul>
        </div>

        <div class="panel panel-info">
            <div class="panel-body">
                <form action=" " method="post" class="form-horizontal" role="form" >
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>

                    <div class="form-group">
                        <div class='col-sm-2 col-lg-3 col-xs-12'>
                            <select name="search[minid]" class="form-control">
                                <option value="1" @if($search['minid'] == 1) selected @endif>主体小程序</option>
                                <option value="2" @if($search['minid'] == 2) selected @endif>商城小程序</option>
                            </select>
                        </div>
                        <div class="col-sm-2 col-lg-3 col-xs-12">
                            <input class="form-control" name="search[keyword]" type="text" value="{{ $search['keyword'] or ''}}" placeholder="搜索关键词，默认：养居益">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-lg-3 search-btn">
                            <div class="btn-input">
                                <input type="submit" class="btn btn-block btn-success" value="搜索">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">估算被索引数：{{ $response['hit_count'] }}&nbsp;&nbsp;&nbsp;&nbsp;<a class='btn btn-info' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.search.submit-pages') }}" style="margin-bottom: 2px">提交页面</a></div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style='width:10%; text-align: center;'>页面标题</th>
                        <th style='width:20%; text-align: center;'>页面摘要</th>
                        <th style='width:10%; text-align: center;'>页面代表图</th>
                        <th style='width:60%; text-align: center;'>页面路径</th>
                    </tr>
                    </thead>
                    @if($response['items'])
                    @foreach($response['items'] as $item)
                        <tr style="text-align: center;">
                            <td>{! $item['title'] !}</td>
                            <td>{! $item['description'] !}</td>
                            <td>
                                <img src="{{$item['image']}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                            </td>
                            <td>{! $item['path'] !}</td>
                        </tr>
                    @endforeach
                    @endif
                </table>
                @if($response['errcode'] != 0)
                <span>当前搜索的错误信息为: {{$response['errmsg']}}</span>
                @endif
            </div>
        </div>
    </div>

@endsection