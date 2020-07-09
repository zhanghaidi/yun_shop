@extends('layouts.base')

@section('content')
@section('title', trans('公众号导入商品'))

<div class="panel panel-info">
    <div class="panel-heading">
        <span>当前位置：</span>
        <a href="#">
            <span>公众号导入商品</span>
        </a>
    </div>
</div>
<div class="alert alert-info alert-important">
    <span>功能介绍:</span>
    <span style="padding-left: 60px;">1. 选择公众号快速导入商品</span>
    <span style="padding-left: 60px;">2. <b style="color: red">导入公众号后点击导入后请不要关闭页面，或者重新刷新页面，这样会导致程序崩溃，造成不可预计的错误</b></span>
    <span style="padding-left: 60px;">3. 导入会把导入公众号的商品信息:商品基本信息，分类，品牌，视频，属性，规格等导入当前公众号</span>
    <span style="padding-left: 60px;">4. 如果数据你的商品描述中添加了大量的文字样式等,导入时需要更改mysql配置,步骤如下:</span>
    <span style="padding-left: 100px;">1. 找到服务器my.conf文件,针对宝塔用户</span>
    <span style="padding-left: 100px;">2. 查找max_allowed_packet,把max_allowed_packet值改为500M,值根据情况来定</span>
</div>

{{--异步上传,节约时间--}}
<div class="form-group">
    <label class="col-sm-2 control-label must">选择公众号</label>
    <div class="col-sm-5 goodsname" >
        <select class="form-control batch_type" id='uniacid' name="genre">
            @foreach($uniAccount as $value)
                <option value="{{ $value['uniacid'] }}">{{ $value['name'] }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class='form-group'>
    <div class="col-sm-12">
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="index()">点击导入</button>
        </div>
    </div>
</div>

<script>
    function index() {
        $.ajax({
            url: "{!! yzWebUrl('plugin.goods-assistant.admin.ImportUniacid.import') !!}",
            type: "post",
            data: {'uniacids':$('#uniacid').val()},
            cache: false,
            success: function (result) {
                alert(result.msg);
                // window.location.reload();
            }
        })
    }
</script>
@endsection('content')
