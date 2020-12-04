@extends('layouts.base')
@section('title', trans('评论管理'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">评论管理</a></li>
        </ul>
    </div>
    <div class='panel panel-default'>
        <div class='panel-body'>
            <form action="" method="get" class="form-horizontal" role="form" id="form2">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.room.commentreplylist"/>
                <input type="hidden" name="id" value="{{$request['id']}}"/>
                <div class="form-group">

                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <select name="search[del_sta]" class="form-control">
                            <option value="">评论状态</option>
                            <option value="1" @if($request['search']['del_sta']=='1') selected @endif>待审核</option>
                            <option value='0' @if($request['search']['del_sta']=='0') selected @endif>正常</option>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                    </div>
                </div>
            </form>
            <div class="clearfix panel-heading" id="goodsTable">
                <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:history.go(-1);">返回</a>
            </div>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr>
                    <th style='width:10%;'>ID</th>
                    <th style='width:15%;'>用户头像/昵称</th>
                    <th style='width:15%;'>评论内容</th>
                    <th style='width:25%;'>评论状态</th>
                    <th style='width:25%;'>评论时间</th>
                    <th style='width:30%;'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($comment_list as $row)
                    <tr>
                        <td>{{ $row['id'] }}</td>
                        <td style="overflow:visible;">
                            <div class="show-cover-img-big" style="position:relative;width:50px;overflow:visible">
                                <img src="{!! tomedia($row['avatarurl']) !!}" alt=""
                                     style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                <img class="img-big" src="{!! tomedia($row['avatarurl']) !!}" alt=""
                                     style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                            </div>
                            {{$row['nickname']}}
                        </td>
                        <td>{{ $row['content'] }}</td>
                        <td>@if ($row['del_sta'] == 1)
                                <span style="color: red">待审核</span> &nbsp;&nbsp;
                                <a class='btn btn-success btn-delete'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.commentverify', ['id' => $row['id'],'type'=>'comment_verify'])}}"
                                   title='通过审核'> 通过审核
                                </a>
                            @else
                                <span style="color: green">正常</span>
                            @endif
                        </td>
                        <td>{{ $row['create_time'] }}</td>
                        <td style="overflow:visible;">
                            <a class='btn btn-default btn-delete'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.commentdel', ['id' => $row['id'],'type'=>'comment_reply_list'])}}"
                               title='删除'>删除
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

    <script type="text/javascript">
        // 查看课程封面大图
        $('.show-cover-img-big').on('mouseover', function () {
            $(this).find('.img-big').show();
        });
        $('.show-cover-img-big').on('mouseout', function () {
            $(this).find('.img-big').hide();
        });
    </script>
@endsection