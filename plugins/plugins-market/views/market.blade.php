@extends('layouts.base')

@section('title', trans('插件安装/升级'))

@section('css')
<link rel="stylesheet" type="text/css" href="{{ plugin_assets('plugins-market', 'assets/css/market.css') }}">
@endsection

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="margin-left: 0px">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="display: inline-block;">
            {{--{{ trans('Yunshop\PluginsMarket::general.name') }}--}}
            插件安装/升级
        </h1>
        <a href="{{yzWebUrl('plugins.get-plugin-data')}}" class="btn btn-warning" style="font-size: 13px;float: right;margin-top: 20px;">
            <i class="fa fa-mail-reply-all"> </i> 返回插件管理
        </a>
    </section>

    <div style="color:#ff2620;">
        （更新插件后，请返回到插件管理页面，将已更新了的插件禁用后再启用）
    </div>
    <!-- Main content -->
    <section class="content">

        <div class="modal" id="showMiddleModal"
             data-backdrop="false" data-keyboard="false"
             role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h5 class="center-block">正在加载中...</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="openModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">填写密钥</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form  method="post" class="form-horizontal form" enctype="multipart/form-data">
                        <div class="modal-body" id="openModalBody">
                            <div class="form-group">
                                <label class="form-control-label">Key:</label>
                                <input type="text" name="plugin[key]" class="form-control" id="key" value="">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">密钥:</label>
                                <input type="text" name="plugin[secret]" class="form-control" id="secret" value="">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                            <input type="submit" name="submit" value="确定" class="btn btn-success " onclick="return formcheck(this)" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @if (session()->has('message'))
            <div class="callout callout-success" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <div class="box">

            <div class="box-body">
                <div class="alert"></div>

                <div class="row">
                    <div class="col-md-6 scrollbox  left-menu-border content-height-scroll" >
                        <ul class="nav nav-sidebar">
                            @foreach($data as $k => $item)
                            <li>
                                <button onclick="jumpToDiv(this, '{{$k}}')" data-status="{{$item['versionStatus']}}" class="button_content" style="background:  #f4f4f4;">
                                    {{--<div class="col-md-2 button_icon">{{$item['version']}}</div>--}}

                                    <div class="col-md-9">
                                        <div class="form-group button_title">
                                            <h5 style="float: left">
                                                {{$item['title']}}
                                                <span style="font-size: 11px">({{$item['version']}})</span>
                                            </h5>
                                        </div>
                                        {{--<div class="form-group button_detail">{{$item['description']}}</div>--}}
                                    </div>
                                    <div class="col-md-3 button_icon">
                                        <span style="font-size: 12px;">
                                        @if ($uninstall)
                                                <label class="upgrade-css" id="versionStatusCss{{$k}}"> <i class="fa fa-arrow-o"></i> 禁止安装 </label>
                                        @else
                                            @if($item['versionStatus'] == 'new')
                                                    <label class="upgrade-css" id="versionStatusCss{{$k}}"> <i class="fa fa-arrow-up"></i> 请升级 </label>
                                            @elseif($item['versionStatus'] == 'preview')
                                                    <label> 预览 </label>
                                            @elseif($item['versionStatus'] == 'installed')
                                                    <label class="installed-css" id="versionStatusCss{{$k}}"> <i class="fa fa-check-square-o"></i> 已安装 </label>
                                            @else
                                                    <label> <i class="fa fa-download"></i> 未安装</label>
                                            @endif
                                        @endif
                                        </span>
                                    </div>
                                    {{--<div class="col-md-2 button_icon ">--}}
                                        {{--<select class="select-css" id="select-css{{$k}}" name="version" onchange="selectVersion(this, '{{$k}}', )">--}}
                                            {{--@foreach($item['versionList'] as $index => $ver)--}}
                                                {{--<option value="{{$ver['id']}}" @if($ver['version'] == $item['version']) selected @endif--}}
                                                    {{--data-size="{{$ver['size']}}" data-description="{{$ver['description']}}" data-index="{{$index}}">--}}
                                                    {{--{{$ver['version']}}--}}
                                                {{--</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-6 main content-height-scroll">

                        @foreach($data as $k => $item)
                        <div id="detail{{$k}}" data-status="{{$item['versionStatus']}}" data-size="{{$item['size']}}" data-name="{{$item['name']}}" data-index="{{$k}}" class="display" style="@if($k==0) display: block; @else display: none;@endif">
                            <div class="form-group button_title">
                                <h4 style="text-align: center">{{$item['title']}}</h4>
                                <p style="text-align: right; font-size: 12px"> ——— {{$item['author']}}</p>
                            </div>

                            <div class="form-group">
                                @if ($uninstall)
                                    <label class="upgrade-css" id="versionStatusCss{{$k}}"> <i class="fa fa-arrow-o"></i> 禁止安装 </label>
                                @else
                                    @if($item['versionStatus'] == 'new')
                                    <button onclick="isUpdated('{{$item['latestVersion']}}', '{{$item['key']}}', '{{$item['secret']}}')" class="btn btn-info" style="height: 32px">
                                        <i class="fa fa-download"> </i> <label style="color:#fff"> 升级 </label>
                                    </button>
                                    @elseif($item['versionStatus'] == 'installed')
                                        <button onclick="" disabled class="btn btn-success" style="height: 32px">
                                            <i class="fa fa-download"> </i> <label> 安装 </label>
                                        </button>
                                    @elseif($item['versionStatus'] == 'preview')
                                        <button onclick="" class="btn btn-success" style="height: 32px">
                                            <i class="fa fa-download"> </i> <label> 预览 </label>
                                        </button>
                                    @else
                                        <button onclick="isDownload('{{$item['key']}}', '{{$item['secret']}}')" class="btn btn-success" style="height: 32px">
                                            <i class="fa fa-download"></i> <label> 安装 </label>
                                        </button>
                                    @endif
                                @endif
                            </div>
                            {{--<div class="form-group">--}}
                                {{--<label class="font-description">请选择版本 :</label>--}}
                                {{--<select class="select-css" id="select-css{{$k}}" name="version" onchange="selectVersion(this, '{{$k}}', )">--}}
                                {{--@foreach($item['versionList'] as $index => $ver)--}}
                                {{--<option value="{{$ver['id']}}" @if($ver['version'] == $item['version']) selected @endif--}}
                                {{--data-size="{{$ver['size']}}" data-description="{{$ver['description']}}" data-index="{{$index}}">--}}
                                {{--{{$ver['version']}}--}}
                                {{--</option>--}}
                                {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                            <div class="form-group">
                                <label class="font-description"> 插件详情：</label><br/>
                                <div class="interval">{{$item['description']}}</div>
                            </div>
                            <div class="form-group">
                                <label class="font-description"> 版本号：</label>
                                <span class="interval" id="versionNumber{{$k}}">{{$item['version']}}</span>
                            </div>
                            <div class="form-group">
                                <label class="font-description"> 版本说明：</label><br/>
                                <div class="interval" id="versionDetail{{$k}}">
                                @foreach($item['versionList'] as $ver)
                                            @if($ver['version'] == $item['version'])
                                                <span data-version="{{$item['version']}}">{!! $ver['description'] !!}</span>
                                            @endif
                                        @endforeach
                                    </div>
                            </div>
                            <div class="form-group">
                                <label class="font-description"> 大小：</label>
                                <span class="interval" id="size{{$k}}">{{$item['size']}}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function () {

            var obj = $('.main').find('div:visible')
            var name = obj.attr('data-name')
            var index = obj.attr('data-index')
            var size = obj.attr('data-size')
            var status = obj.attr('data-status')
            var version = $('#versionDetail' + index + ' span').attr('data-version')

            $('#openModalBody').append('<div id="modal-hidden-div">' +
                '<input type="hidden" data-status="'+ status +'" value="'+ name +'" name="pluginData[name]">' +
                '<input type="hidden" value="'+ version +'" name="pluginData[version]">' +
                '</div>')

        })
    </script>

    {{-- 下载js --}}

    <script type="text/javascript">

        function showMiddleModal() {
            $('#showMiddleModal').modal('show')
        }
        function showAlert($message, $css) {

            return $('.alert').html($message).addClass($css).show().delay(1500).fadeOut();
            //return $('.alert').html($message).addClass($css).show();
        }
        function formcheck(event) {
            var $name = $(':input[name="pluginData[name]"]').val()
            var $version = $(':input[name="pluginData[version]"]').val()
            let status = $(':input[name="pluginData[version]"]').attr('data-status')
            let tip = status == 'new' ? '更新' : '下载';

            var $url = "{{yzWebUrl('plugin.plugins-market.Controllers.plugin.readyToDownload')}}";
            $url = $url.replace(/\amp;/g, '')

            if ($(':input[name="plugin[key]"]').val() == '' || $(':input[name="plugin[secret]"]').val() == '') {
                if($(':input[name="plugin[key]"]').val() == '') {
                    Tip.focus(':input[name="plugin[key]"]', 'Key 不能为空');
                }else {
                    Tip.focus(':input[name="plugin[secret]"]', '密钥不能为空')
                }
                return false;
            }

            showMiddleModal()
            $.ajax({
                type : 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                url : $url,
                data : {
                    'plugin' : {'name' : $name, 'version' : $version} ,
                    'keyData' : {'key' : $(':input[name="plugin[key]"]').val(), 'secret' : $(':input[name="plugin[secret]"]').val()}
                    },
                dataType : 'json',
                success : function (msg) {
                    $('#showMiddleModal').modal('hide');
                    switch (msg['code']){
                        case 0:
                            $('#openModal').modal('hide');
                            showAlert(tip+'成功', 'alert-success')
                            location.reload();
                            console.log('download success ',msg);
                            break;
                        case -2 :
                            $('#key').val('');
                            $('#secret').val('');
                            $('#openModal').modal('show');
                            break;
                        case -3:
                            $('#openModal').modal('hide');
                            showAlert(msg['msg'], 'alert-danger')
                            break;
                        default:
                            console.log(msg);
                            break;
                    }
                },
                error : function (msg) {
                    console.log(msg)
                    $('#showMiddleModal').modal('hide')
                }
            })
            return false;
        }

        function isDownload(key, secret) {
            if(confirm('您确定要下载这个插件吗？')) {
                //检查是否已有key 和密钥
                var $name = $(':input[name="pluginData[name]"]').val()
                var $version = $(':input[name="pluginData[version]"]').val()

                var $url = "{{yzWebUrl('plugin.plugins-market.Controllers.plugin.check')}}";
                $url = $url.replace(/\amp;/g, '')

                showMiddleModal();
                $.ajax({
                    type : 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    url : $url,
                    data : {'plugin[name]' : $name, 'plugin[version]' : $version},
                    dataType : 'json',
                    success : function (msg) {
                        console.log(msg)
                        $('#showMiddleModal').modal('hide');
                        switch (msg['code']){
                            case -2 :
                                if (typeof key != "undefined" || typeof secret != "undefined") {
                                    $('#key').val(key);
                                    $('#secret').val(secret);
                                } else {
                                    $('#key').val('');
                                    $('#secret').val('');
                                }
                                $('#openModal').modal('show');
                                break;
                            case 0:
                                showAlert('下载成功', 'alert-success')
                                location.reload();
                                console.log('download success ',msg);
                                break;
                            default:
                                console.log(msg);
                                break;
                        }
                    },
                    error : function (error) {
                        $('#showMiddleModal').modal('hide')
                        console.log(error)
                    }
                })
            }
        }

        function jumpToDiv(obj, $index){
            $('.installed-css').css('color' , '#00a65a')
            $('.upgrade-css').css('color' , '#00c0ef')
            var version_status = $(obj).attr('data-status')
            if(version_status == 'new' || version_status == 'installed') {
                $('#versionStatusCss' + $index).css('color', '#f5f5f5');
            }
            $('.button_content').attr('style', 'background:  #f4f4f4; ')
            $(obj).attr('style', 'background:  rgba(11, 70, 224, 0.61); color:#f5f5f5')
            $('.display').attr('style', 'display:none;')
            $('#detail' + $index).attr('style', 'display:block;')
           // $('.select-css').attr('style', 'color:rgba(216, 217, 220, 0.77);')
            $('#select-css' + $index).attr('style', 'color: rgba(17, 33, 78, 0.77);')
            var name = $('#detail' + $index).attr('data-name')
            var version = $('#versionDetail' + $index + ' span').attr('data-version')
            $(':input[name="pluginData[name]"]').val(name)
            $(':input[name="pluginData[version]"]').val(version)
        }

        function selectVersion(obj, $detail_index) {

            var  $versionDescription = $(obj).find('option:selected').attr('data-description')
            var size = $(obj).find('option:selected').attr('data-size')
            $('#versionDetail' + $detail_index).html('<span id="versionDetail"'+ $detail_index +'>' + $versionDescription +'</span>')
            $('#size' + $detail_index).html(size)

            var name = $('#detail' + $detail_index).attr('data-name')
            var version = $(obj).find('option:selected').text().trim();
            //console.log('select version', version)
            $(':input[name="pluginData[version]"]').val(version)

        }

    </script>
    {{-- 更新js --}}
    <script type="text/javascript">
        function isUpdated($updateVersion, key, secret) {

            if(confirm('您确定要更新这个插件吗？')) {
                //检查是否已有key 和密钥
                var $name = $(':input[name="pluginData[name]"]').val()
                var $url = "{{yzWebUrl('plugin.plugins-market.Controllers.plugin.checkIsUpdate')}}";
                $url = $url.replace(/\amp;/g, '')
                showMiddleModal()
                $.ajax({
                    type : 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    url : $url,
                    data : {
                        'plugin[name]' : $name,
                        'plugin[version]' : $updateVersion,
                        //'plugin[url]' : $updateUrl
                    },
                    dataType : 'json',
                    success : function (msg) {
                        console.log(msg)
                        $('#showMiddleModal').modal('hide')

                        switch (msg['code']){
                            case -2 :
                                if (typeof key != "undefined" || typeof secret != "undefined") {
                                    $('#key').val(key);
                                    $('#secret').val(secret);
                                } else {
                                    $('#key').val('');
                                    $('#secret').val('');
                                }
                                //更新版本
                                $(':input[name="pluginData[version]"]').val($updateVersion)
                                $('#openModal').modal('show');
                                break;
                            case 0:
                                showAlert('更新成功', 'alert-success')
                                location.reload();
                                console.log('download success ',msg);
                                break;
                            default:
                                console.log(msg);
                                break;
                        }
                    },
                    error : function (error) {
                        $('#showMiddleModal').modal('hide');

                        console.log(error)
                    }
                })
            }

        }
    </script>
{{--<script type="text/javascript" src="{{ plugin_assets('plugins-market', 'assets/js/market.js') }}"></script>--}}

@endsection
