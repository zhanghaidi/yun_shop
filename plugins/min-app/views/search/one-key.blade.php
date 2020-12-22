@extends('layouts.base')
@section('title', '被收录情况')
@section('content')
    <div class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="{{yzWebUrl('plugin.min-app.Backend.Controllers.search.site-search')}}">收录查询</a></li>
                <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;自定提交部分特定小程序页面</a></li>
            </ul>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <a class='btn btn-info' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.search.one-key', ['type' => 1]) }}" style="margin-bottom: 2px">社区详情</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a class='btn btn-info' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.search.one-key', ['type' => 2]) }}" style="margin-bottom: 2px">文章详情</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a class='btn btn-info' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.search.one-key', ['type' => 3]) }}" style="margin-bottom: 2px">课程详情</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a class='btn btn-info' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.search.one-key', ['type' => 4]) }}" style="margin-bottom: 2px">商品详情</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a class='btn btn-info' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.search.one-key', ['type' => 5]) }}" style="margin-bottom: 2px">穴位详情</a>&nbsp;&nbsp;&nbsp;&nbsp;

                <div class="form-group" style="margin-top: 10px">
                    <label class="radio-inline">
                        <input type="radio" name="search[switch]" value="1" @if($set['search']['switch'] == 1) checked @endif> 自动推送开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="search[switch]" value="0" @if($set['search']['switch'] == 0) checked @endif> 自动推送关闭
                    </label>
                    <span class="help-text">修改配置后，请刷新页面</span>
                </div>
            </div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style='width:50%; text-align: center;'>名称</th>
                        <th style='width:20%; text-align: center;'>页面</th>
                        <th style='width:20%; text-align: center;'>参数</th>
                        <th style='width:10%; text-align: center;'>提交结果</th>
                    </tr>
                    </thead>
                    @if($type == 0)
                        <tr id="1">
                            <td class="app">
                                穴位查询
                                <input type="hidden" name="minid" value="1">
                            </td>
                            <td class="page">pages/acupoint/list/acupoint</td>
                            <td class="query"></td>
                            <td class="status"></td>
                        </tr>
                        <tr id="2">
                            <td class="app">
                                课程列表
                                <input type="hidden" name="minid" value="1">
                            </td>
                            <td class="page">pages/course/aloneCurriculumList/aloneCurriculumList</td>
                            <td class="query"></td>
                            <td class="status"></td>
                        </tr>
                        <tr id="3">
                            <td class="app">
                                防伪查询
                                <input type="hidden" name="minid" value="1">
                            </td>
                            <td class="page">pages/user/anti/anti</td>
                            <td class="query"></td>
                            <td class="status"></td>
                        </tr>
                        <tr id="4">
                            <td class="app">
                                体质测试答题页
                                <input type="hidden" name="minid" value="1">
                            </td>
                            <td class="page">pages/homework/test/homework</td>
                            <td class="query"></td>
                            <td class="status"></td>
                        </tr>
                        <tr id="5">
                            <td class="app">
                                商城
                                <input type="hidden" name="minid" value="2">
                            </td>
                            <td class="page">pages/template/shopping/index</td>
                            <td class="query"></td>
                            <td class="status"></td>
                        </tr>
                        <tr id="6">
                            <td class="app">
                                防伪查询
                                <input type="hidden" name="minid" value="2">
                            </td>
                            <td class="page">pages/user/anti/anti</td>
                            <td class="query"></td>
                            <td class="status"></td>
                        </tr>
                    @endif
                    @if($type == 1)
                    @foreach($post as $item)
                        <tr id="{{ $item['id'] }}">
                            <td class="app">
                                社区帖子 - 标题:{{ $item['title'] }}
                                <input type="hidden" name="minid" value="1">
                            </td>
                            <td class="page">pages/community/detail/detail</td>
                            <td class="query">id={{ $item['id'] }}</td>
                            <td class="status"></td>
                        </tr>
                    @endforeach
                    @endif
                    @if($type == 2)
                    @foreach($article as $item)
                        <tr id="{{ $item['id'] }}">
                            <td class="app">
                                文章 - 标题:{{ $item['title'] }}
                                <input type="hidden" name="minid" value="1">
                            </td>
                            <td class="page">pages/rumours/article/article</td>
                            <td class="query">tid={{ $item['id'] }}</td>
                            <td class="status"></td>
                        </tr>
                    @endforeach
                    @endif
                    @if($type == 3)
                    @foreach($room as $item)
                        <tr id="{{ $item['id'] }}">
                            <td class="app">
                                课程 - 名称:{{ $item['name'] }}
                                <input type="hidden" name="minid" value="1">
                            </td>
                            <td class="page">pages/course/CouRse/index</td>
                            <td class="query">tid={{ $item['id'] }}</td>
                            <td class="status"></td>
                        </tr>
                    @endforeach
                    @endif
                    @if($type == 4)
                    @foreach($goods as $item)
                        <tr id="{{ $item['id'] }}">
                            <td class="app">
                                商品 - 标题:{{ $item['title'] }}
                                <input type="hidden" name="minid" value="2">
                            </td>
                            <td class="page">pages/shopping/detail/details</td>
                            <td class="query">goods_id={{ $item['id'] }}</td>
                            <td class="status"></td>
                        </tr>
                    @endforeach
                    @endif
                    @if($type == 5)
                    @foreach($acupoint as $item)
                        <tr id="{{ $item['id'] }}">
                            <td class="app">
                                穴位 - 名称:{{ $item['name'] }}
                                <input type="hidden" name="minid" value="1">
                            </td>
                            <td class="page">pages/acupoint/detail/detail</td>
                            <td class="query">tid={{ $item['id'] }}</td>
                            <td class="status"></td>
                        </tr>
                    @endforeach
                    @endif
                </table>
            </div>
        </div>
    </div>

<script>
$(function(){
    _ei = 1;
    $("table tr").each(function(){
        setTimeout(() => {
            _switch = $('input[name="search[switch]"]:checked').val();
            if (_switch != 1) {
                return ;
            }

            _id = $(this).attr('id');
            _minid = $(this).find('.app').find('input[name="minid"]').val();
            if (_minid == undefined || _minid <= 0 || _minid > 2) {
                return ;
            }
            _page = $(this).find('.page').html();
            _query = $(this).find('.query').html();
            _status = $(this).find('.status').html();
            if (_status != '') {
                return ;
            }
            _data = {
                'minid': _minid,
                'path': _page,
                'query': _query,
                'isajax': 1,
            };
            _url = "{{ yzWebUrl('plugin.min-app.Backend.Controllers.search.submit-pages') }}";
            _url = _url.replace(/&amp;/g, '&');
            $.post(_url, {page: _data}, function(res){
                $('#' + _id).find('.status').html(res.msg);
            });
        }, _ei * 1000);
        _ei += 1;
    });

    $.get('/static/pages/share.json', function(data) {
        if (!('options' in data)) {
            return false;
        }
        _pageOpt = '<option value="">请选择页面</option>';
        for (var i = 0; i < data.options.length; i++) {
            _list = data.options[i];
            if (!('value' in _list) || !('name' in _list)) {
                continue;
            }
            if (_list.value == '') {
                _pageOpt += '<option value="' + _list.value + '" selected>' + _list.name + '</option>'
            } else {
                _pageOpt += '<option value="' + _list.value + '">' + _list.name + '</option>'
            }
        }
        $('select[name="page[mainapp][path]"]').html(_pageOpt);
    });

    $('select[name="page[mainapp][path]"]').on('change', function(){
        _desc = '页面路径: ';
        _desc += $(this).val();
        $(this).next('span').html(_desc);
    });

    $('input[name="page[minid]"]').on('change', function(){
        if ($(this).val() == 1) {
            $('.mainappSelect').show();
            $('.shopappSelect').hide();
        } else {
            $('.mainappSelect').hide();
            $('.shopappSelect').show();
        }
    });

    $('input[name="search[switch]"]').change(function(){
        _switch = $('input[name="search[switch]"]:checked').val();
        _url = "{{ yzWebUrl('plugin.min-app.Backend.Controllers.search.one-key') }}";
        _url = _url.replace(/&amp;/g, '&');
        _data = {
            'search': {
                'switch': _switch,
            }
        };
        $.post(_url, _data, function(res){
            if (res.result == 1) {
            } else {
                alert('设置出错了');
            }
        });
    });
});
</script>
@endsection
