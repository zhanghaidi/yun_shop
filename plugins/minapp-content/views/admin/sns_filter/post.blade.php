@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.post.index')}}">话题管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-board.index')}}">话题版块</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-filter.post')}}">敏感词库</a></li>
                </ul>
            </div>

        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.sns-filter.category') }}" class="btn btn-info">添加类目</a>
            </div>

            <ul class="add-shopnav nav" role="tablist" id="myNav">
                @foreach($data as $k => $v)
                <li @if($k == 0) class="active" @endif role="presentation">
                    <a href="#filter_nav{{$v['id']}}" aria-controls="filter_nav{{$v['id']}}" role="tab" data-toggle="tab">{{$v['title']}}</a>
                </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($data as $k => $v)
                <div class="tab-pane @if($k == 0) active @endif " id="filter_nav{{$v['id']}}" role="tabpanel">
                    <form id="form{{$v['id']}}" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                        <input type="hidden" name="data[id]" value="{{$v['id']}}">

                        <textarea class="form-control" rows="50" name="data[content]">{{$v['content']}}</textarea>

                        <div class="center-block text-center" style="margin-top: 10px;">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary">
                        </div>
                    </form>
                </div>
                @endforeach
            </div>

        </div>
        <div class="panel-footer">
            <div class="text-center">
                敏感词添加(敏感词请以-分割开.中间不能有空格和其他符号。请严格注意格式。否则会导致评论无内容)
            </div>
        </div>
    </div>
</div>

<script language="JavaScript">
$('#myNav a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
</script>
@endsection

