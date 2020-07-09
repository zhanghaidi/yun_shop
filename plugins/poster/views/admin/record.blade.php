@extends('layouts.base')
@section('title', '海报生成记录')

@section('content')
    <section class="content">
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="{{yzWebUrl('plugin.poster.admin.poster.index')}}">海报管理</a></li>
                <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;海报生成记录</a></li>
            </ul>
        </div>

      {{--  <form method="post" action="{!! yzApiUrl('plugin.yop-pay.api.pic.getUrl', ['test_uid' => 70]) !!}" enctype="multipart/form-data" class="form-horizontal form">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片</label>
            <input type="file" name="file" class="form-control" value="">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <input type="submit" name="submit" value="提交" class="btn btn-primary" data-original-title="" title="">
        </form>
--}}


        <form action="" method="post" onsubmit="return formcheck(this)">
            <div class='panel panel-default'>
                <div class='panel-body'>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>会员id</th>
                            <th>海报路径</th>
                            <th>生成时间</th>
                            <th style="width:260px;">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($posterRecord as $posterRecord)
                            <tr>
                                <td>
                                    {{$posterRecord['member_id']}}
                                </td>
                                <td >{{$posterRecord['url']}}</td>

                                <td>{{date( "Y-m-d H:i:s",+ $posterRecord['created_at'])}}</td>

                                <td>
                                    <a class='btn btn-default' href="{{yzWebUrl('plugin.poster.admin.posterRecord.remake', array('id'=>$posterRecord['id'],'member_id'=>$posterRecord['member_id']))}}" title='重新生成海报'><i class='fa fa-file'></i></a>
                                    <a class='btn btn-default'   href="{{yzWebUrl('plugin.poster.admin.posterRecord.delete', array('id'=>$posterRecord['id']))}}"  title='删除' onclick="return confirm('确认删除此会员生成的海报吗？');return false;"><i class='fa fa-remove'></i></a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    {!!$pager!!}
                </div>
            </div>
        </form>

    </section><!-- /.content -->
@endsection