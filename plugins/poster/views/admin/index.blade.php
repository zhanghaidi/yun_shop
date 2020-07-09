@extends('layouts.base')
@section('title', '海报列表')

@section('content')
    <section class="content">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">海报管理</a></li>
            </ul>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="POST" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">海报名称</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="title" type="text">
                        </div>
                    </div>
<!--
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">海报类型</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <select name="type" class='form-control'>
                                <option value="" selected></option>
                                <option value="2">长期海报</option>
                                <option value="1">活动海报</option>
                            </select>
                        </div>
                    </div>
-->
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-lg-2 col-md-offset-1">
                            <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <form action="" method="post" onsubmit="return formcheck(this)">
            <div class='panel panel-default'>
                <div class='panel-heading'>海报管理 (总数: {{$posters_num}})</div>
                <div class='panel-body'>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>海报名称</th>
                                <th>关键词</th>
        <!--预留                 <th>海报类型</th>  -->
                                <th>扫描数</th>
                                <th>引流注册人数</th>
                                <th>状态</th>
                                <th>会员中心显示</th>
                                <th style="width:260px;">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posters as $poster)
                                <tr>
                                    <td>
                                        {{$poster['title']}}
                                    </td>
                                    <td>{{$poster['keyword']}}</td>
        <!--预留                            <td>
                                        @if($poster['type']==1)
                                            <label class='label label-primary'>长期海报</label>
                                        @elseif($poster['type']==2)
                                            <label class='label label-success'>活动海报</label>
                                        @endif
                                    </td>
        -->
                                    <td>{{$poster['scan_count']}}</td>
                                    <td>{{$poster['award_count']}}</td>
                                    <td>
                                        @if($poster['status'] == 1)
                                            <label class="label label-success">启用</label>
                                        @else
                                            <label class="label label-warning">禁用</label>
                                        @endif
                                    </td>
                                    <td>
                                        @if($poster['center_show'] == 1)
                                            <label class="label label-success">启用</label>
                                        @else
                                            <label class="label label-warning">关闭</label>
                                        @endif
                                    </td>
                                    <td>
                                        <a class='btn btn-default' href="{{yzWebUrl('plugin.poster.admin.poster-record.index', array('poster_id'=>$poster['id']))}}"  title='海报生成记录'><i class='fa fa-file-image-o'></i></a>
                                        <a class='btn btn-default' href="{{yzWebUrl('plugin.poster.admin.poster-scan.index', array('poster_id'=>$poster['id']))}}"  title='扫码记录'><i class='fa fa-qrcode'></i></a>
                                        <a class='btn btn-default' href="{{yzWebUrl('plugin.poster.admin.poster-award.index', array('poster_id'=>$poster['id']))}}"  title='奖励记录'><i class='fa fa-money'></i></a>
                                        <a class='btn btn-default' href="{{yzWebUrl('plugin.poster.admin.poster.edit', array('poster_id'=>$poster['id']))}}" title='编辑'><i class='fa fa-edit'></i></a>
                                        <a class='btn btn-default'  href="{{yzWebUrl('plugin.poster.admin.poster.delete', array('poster_id'=>$poster['id']))}}"  title='删除' onclick="return confirm('确认删除此海报吗？');return false;"><i class='fa fa-remove'></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5">
                                    <a class='btn btn-primary' href="{{yzWebUrl('plugin.poster.admin.poster.add')}}"><i class="fa fa-plus"></i> 添加海报</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    {!!$pager!!}
                </div>
            </div>
        </form>

    </section><!-- /.content -->
@endsection