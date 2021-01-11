@extends('layouts.base')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">活码列表</div>

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="plugin" id="form_do"/>
                    <input type="hidden" name="route" value="plugin.activity-qrcode.admin.activity.index" id="route"/>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control" name="search[name]"
                                   value="{{$search['name']}}" placeholder="活码名称关键字"/>
                        </div>
                    </div>

                    {{--<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                        <div class="">
                            <input type="text" placeholder="搜索类型" class="form-control" name="search[type]"
                                   value="{{$search['type']}}"/>
                        </div>
                    </div>--}}

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">

                        <div class="time">
                            <select name='search[search_time]' class='form-control'>
                                <option value='0' @if($search['search_time']=='0') selected @endif>不搜索时间</option>
                                <option value='1' @if($search['search_time']=='1') selected @endif>搜索时间</option>
                            </select>
                            <div class="search-select">
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                                'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                                'start'=>0,
                                'end'=>0
                                ], true) !!}
                            </div>
                        </div>

                    </div>
                    <div class="form-group  col-xs-12 col-md-12 col-lg-6">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                        <div class="">
                            <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>


                        </div>
                    </div>

                </form>

            </div>


        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <a class='btn btn-info' href="{{ yzWebUrl('plugin.activity-qrcode.admin.activity.add') }}" style="margin-bottom: 2px">添加活码</a>&nbsp;&nbsp;&nbsp;&nbsp;

                <div class="panel-heading">记录总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%; text-align: center;'>创建时间</th>
                            <th style='width:6%; text-align: center;'>活码名称</th>
                            <th style='width:6%; text-align: center;'>活码标题</th>
                            <th style='width:6%; text-align: center;'>活码类型</th>
                            <th style='width:12%; text-align: center;'>今日扫码人数</th>
                            <th style='width:12%; text-align: center;'>累计扫码人数</th>
                            <th style='width:12%; text-align: center;'>二维码状态</th>
                            <th style='width:12%; text-align: center;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->created_at }}</td>

                                <td style="text-align: center;">
                                    {{ $list->activity_name }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $list->title }}
                                </td>

                                <td style="text-align: center;">
                                    @if($list->switch_type == 1) 平均切换
                                    @else 群满切换
                                    @endif
                                </td>

                                <td style="text-align: center;">


                                </td>
                                <td style="text-align: center;">
                                    {{$list->has_many_qrcode_count}}<br>
                                    {{$list->timeout}}

                                </td>
                                <td style="text-align: center;">
                                    总数量：<span style="color: green">{{$list->has_many_qrcode_count}}</span><br>
                                    已满：<span style="color:orange">{{$list->full_count}}</span><br>
                                    到期：<span style="color: red">{{$list->timeout_count}}</span>
                                </td>

                                <td style="text-align: center;">
                                    <a class='btn btn-default clock_link' href="javascript:;"
                                       data-clock_link="{{ $list->clock_link }}"
                                       title='分享'><i class='fa fa-share'></i>&nbsp;&nbsp;分享
                                    </a>

                                    <a class='btn btn-default' title="二维码管理" href="{{ yzWebUrl('plugin.activity-qrcode.admin.qrcode.index', array('id' => $list->id)) }}" style="margin-bottom: 2px"><i class="fa fa-qrcode"></a>
                                    <a class='btn btn-default nav-edit' title="编辑活码" href="{{ yzWebUrl('plugin.activity-qrcode.admin.activity.edit', array('id' => $list->id)) }}"><i class="fa fa-edit"></i></a>
                                    <a class='btn btn-default nav-del' title="删除活码" href="{{ yzWebUrl('plugin.activity-qrcode.admin.activity.deleted', array('id' => $list->id)) }}" onclick="return confirm('确认删除此活码？');return false;"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>
        <div style="width:100%;height:150px;"></div>
        <div id="qrcode" ref="qrcode" style="display:none;"></div>
        <img id='clock_qr'>

        <script src="{{resource_get('static/js/qrcode.min.js')}}"></script>
        <script type="text/javascript">

            $('.clock_link').click(function (e) {
                qrcodeScan(e.target.dataset.clock_link, 'clock_qr')
            })

            function qrcodeScan(url, name) {//生成二维码

                try {
                    let qrcode = new QRCode('qrcode', {
                        width: 200,  // 二维码宽度
                        height: 200, // 二维码高度
                        render: 'image',
                        text: url
                    });
                } catch (e) {
                    console.log('报错了')
                }
                var data = $("canvas")[$("canvas").length - 1].toDataURL().replace("image/png", "image/octet-stream;");
                // $('#' + name + '').attr('src', data);
                alertMask($("canvas")[$("canvas").length - 1].toDataURL(),url)
                // this.img = data;
            }
            //分享弹出框  复制功能
            function alertMask(imgUrl,clock_link) {
                let template = `<div id="my_mask" style="position: fixed;left: 0;right: 0;top: 0;bottom: 0;background: rgba(0,0,0,0.7);">
                   <div class="centent" style="display: flex;flex-direction: column;justify-content: center;align-items: center; width: 500px;height: 600px;position: absolute;left: 50%;top: 50%;transform: translate(-50%,-50%);background: #fff;">
                    <img src="${imgUrl}" style="width: 450px;height: 450px;margin-top: 30px;font-size: 28px;" alt="">
                    <span class="btn btn-default copy_links" style="margin-top: 25px;" title="复制链接">复制链接</span>
                   </div>
                </div>`
                $('body').append(template);
                $('#my_mask').click(function () {
                    $('#my_mask').remove();
                })
                $('.copy_links').click(function (e) {
                    e.preventDefault();//阻止浏览器的默认行为
                    e.stopPropagation();
                    copy(clock_link);
                })
                function copy(val) {
                    const input = document.createElement('input');
                    document.body.appendChild(input);
                    input.setAttribute('value', val);
                    input.select();
                    if (document.execCommand('copy')) {
                        document.execCommand('copy');
                        alert('复制成功');
                        $('#my_mask').remove();
                    }
                    document.body.removeChild(input);
                }
            }
            // 查看课程封面大图
            $('.show-cover-img-big').on('mouseover', function () {
                $(this).find('.img-big').show();
            });
            $('.show-cover-img-big').on('mouseout', function () {
                $(this).find('.img-big').hide();
            });
        </script>

@endsection('content')
