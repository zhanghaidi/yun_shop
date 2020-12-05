
@extends('layouts.base')
@section('title', trans('企业微信基础设置'))
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-heading'>客户端设置</div>
                    <div class='panel-body'>



                    </div>



                    </div>

                    <div class='panel-heading'>关联公众号</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号关注链接</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[follow_link]" class="form-control" value="{{ $set['follow_link'] }}"/>
                            </div>
                        </div>
                        {{--                        fixBy-wk-20201126 增加企业微信加入群聊链接设置 灸师企业微信 --}}
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">企业微信课程群聊链接</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[qy_wecht_group_link]" class="form-control" value="{{ $set['qy_wecht_group_link'] }}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">灸师企业微信</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[jiushi_qy_wecht_link]" class="form-control" value="{{ $set['jiushi_qy_wecht_link'] }}"/>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

