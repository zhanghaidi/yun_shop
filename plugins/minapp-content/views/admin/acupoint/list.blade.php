@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if(strpos(\YunShop::request()->route, '.acupoint.') !== false) class="active" @endif><a href="{{yzWebUrl('plugin.minapp-content.admin.acupoint.index')}}">穴位列表</a></li>
                    <li @if(strpos(\YunShop::request()->route, '.meridian.') !== false) class="active" @endif><a href="{{yzWebUrl('plugin.minapp-content.admin.meridian.index')}}">经络列表</a></li>
                </ul>
            </div>

            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">1穴位ID:</div>
                        <input type="text" placeholder="请输入穴位ID搜索" value="{{$search['id']}}" name="search[id]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">经脉名/穴位名:</div>
                        <input type="text" placeholder="输入经脉名/穴位名进行搜索" value="{{$search['keyword']}}" name="search[keyword]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.acupoint.edit') }}" class="btn btn-info">添加穴位</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>字母</th>
                        <th>穴位图</th>
                        <th>穴位名</th>
                        <th>穴位拼音</th>
                        <th>所属经络</th>
                        <th>小程序路径</th>
                        <th>穴位类别</th>
                        <th>经验取穴</th>
                        <th>穴位主调</th>
                        <th>音频</th>
                        <th>视频</th>
                        <th>笔记</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td width="50">{{$value['id']}}</td>
                        <td width="50">{{$value['chart']}}</td>
                        <td width="80">
                            <a href="{{ tomedia($value['image']) }}" target="_blank">
                                <img src="{{tomedia($value['image'])}}" width="60">
                            </a>
                        </td>
                        <td width="80">{{$value['name']}}</td>
                        <td width="100">
                            <input type="text" value="{{$value['zh']}}" name="zh[{{$value['id']}}]" class="form-control zhinput">
                            <input type="hidden" value="{{$value['zh']}}" name="zhold[{{$value['id']}}]" class="form-control">
                        </td>
                        <td width="200">{{$value['jingluo']}}</td>
                        <td width="100"><a class="label label-primary btn js-clip"  data-clipboard-text="{{$value['page']}}">点击复制</a></td>
                        <td width="100">{{$value['type']}}</td>
                        <td width="350"><a href="{{ yzWebUrl('plugin.minapp-content.admin.examination.status', ['id' => $value['id'], 'action' => 'stop']) }}">{{$value['get_position']}}</a></td>
                        <td width="350"><a href="{{ yzWebUrl('plugin.minapp-content.admin.examination.status', ['id' => $value['id'], 'action' => 'stop']) }}">{{$value['effect']}}</a></td>
                        <td width="100">
                        @if($value['audio'])
                            <audio style="width: 100px;height: 60px" controls><source src="{{tomedia($value['audio'])}}"></audio>
                        @endif
                        </td>
                        <td width="150">
                            <a href="{{tomedia($value['video'])}}" target="_blank"><video width="150px" height="60px"> <source src="{{tomedia($value['video'])}}"> </video></a>
                        </td>
                        <td width="50">
                            <a class="btn-link" href="{{ yzWebUrl('plugin.minapp-content.admin.acupoint-replys.index', ['id' => $value['id']]) }}"><i class="fa fa-comment-o"></i> {{$value['comment_nums']}} </a>
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.acupoint.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.acupoint.delete', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <button class="btn btn-warning" type="submit" id="updatezh">更新拼音</button>
        </div>
        {!! $pager !!}
    </div>
</div>

<script language="JavaScript">
$(function () {
    $('#updatezh').on('click', function(){
        _data = [];
        $('.zhinput').each(function(){
            _val = $(this).val();
            _source = $(this).next().val();
            if (_val == _source) {
                return;
            }

            _id = $(this).attr('name');
            _id = _id.replace('zh[','');
            _id = _id.replace(']','');
            if (_id <= 0){
                return;
            }
            _data.push({id:_id,zh:_val})
        });

        if (_data.length == 0) {
            return ;
        }

        _url = "{{ yzWebUrl('plugin.minapp-content.admin.acupoint.edit') }}";
        _url = _url.replace(/&amp;/g, '&');
        $.post(_url, {zh_data:_data}, function(res) {
            alert(res.msg);
            location.reload();
        });
    });
});
</script>
@endsection

