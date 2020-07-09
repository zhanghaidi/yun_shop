@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">添加分类</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类名称</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="category[name]" class="form-control"
                                       value="{{ $category->name }}"/>
                            </div>
                        </div>
                        <input type="hidden" name="category[member_level_id_limit]" value="0">
                        {{--<div class="form-group">--}}
                        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级</label>--}}
                        {{--<div class="col-sm-9 col-xs-12">--}}
                        {{--<select id="m_level" name='category[member_level_id_limit]' class='form-control'>--}}
                        {{--<option value='0'>全部</option>--}}
                        {{--<!--注意, 下面的值是yz_member_level中的id值, 而不是level值-->--}}
                        {{--@foreach ($levels as $level)--}}
                        {{--<option value='{{ $level['id'] }}' @if($level['id'] == $category->member_level_id_limit) selected @endif>{{ $level['level_name'] }}</option>--}}
                        {{--@endforeach--}}
                        {{--</select>--}}
                        {{--</div>--}}
                        {{--</div>--}}


                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"/>
                                <input type="button" name="back" onclick='history.back()' style='margin-left:10px;'
                                       value="返回列表" class="btn btn-default col-lg-1"/>
                            </div>
                        </div>


                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
