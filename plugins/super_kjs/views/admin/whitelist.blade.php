@extends('layouts.base')

@section('content')
@section('title', trans('白名单'))

<div class="w1200 m0a">
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">IP白名单</a></li>
            </ul>
        </div>
        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="alert alert-info">
                        为了系统的安全性，请将第三方插件的服务器IP地址保存至白名单
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">IP地址</label>
                        <div class="col-sm-5" >
                            <input type="text" name="ip_address" class="form-control">
                            <span class="help-block">例如：127.0.0.1</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-xs-12 col-sm-9 col-md-10">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th class="col-md-2">IP地址</th>
                            <th class="col-md-2">创建时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ( $list['data'] as  $ip_address )
                            <tr>
                                <td>{{ $ip_address['id'] }}</td>
                                <td>{{ $ip_address['ip_address'] }}</td>
                                <td>{{ $ip_address['created_at'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {!! $pager !!}

                </div>
            </div>

        </form>


    </div>
</div>
@endsection

