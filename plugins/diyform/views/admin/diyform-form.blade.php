@extends('layouts.base')

@section('content')
@section('title', trans('添加自定义表单'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">添加自定义表单</a></li>

    </ul>
</div>


<div class='panel panel-default'>
    <form action="" method="post" class="form-horizontal form">
        <div class="col-sm-12">

            <div class="form-group">
                <label class="col-sm-2 control-label must" style='width:110px;text-align: left;padding-left:22px;'  >表单名称</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="tp_title" class="form-control tp_title" value="{{$item['title']}}" placeholder="表单名称，例：预约摄影" data-rule-required='true' />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label must" style='width:110px;text-align: left;padding-left:22px;'  >允许提交次数</label>
                <div class="col-sm-9 col-xs-12">

                    <label class="radio-inline">
                        <input type="radio" name="tp_submit_number" value="1" @if ($item['tp_submit_number'] == 1) checked="checked" @endif />
                        单次
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="tp_submit_number" value="0" @if ($item['tp_submit_number'] == 0) checked="checked" @endif />
                        多次
                    </label>

{{--                    <input type="radio" name="tp_submit_number" class="form-control tp_title" value="{{$item['tp_submit_number']}}" placeholder="允许提交次数" data-rule-required='true' />--}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label must" style='width:110px;text-align: left;padding-left:22px;'  >表单图片</label>
                <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('tp_thumb', $item['thumb']) !!}
                    <span class="help-block">建议尺寸：375*154 </span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label must" style='width:110px;text-align: left;padding-left:22px;'>详情介绍</label>
                <div class="col-sm-9 col-xs-12 col-md-9">
                    {!! yz_tpl_ueditor('tp_description', $item['description']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label must" style='width:110px;text-align: left;padding-left:22px;'>分享描述</label>
                <div class="col-sm-9 col-xs-12">
                    <textarea name="tp_share_description" class="form-control" rows="6">{{ $item['share_description'] }}</textarea>
                    <span class='help-block'>注:用户分享时,显示的分享标题为表单名称,分享图片为表单图片</span>
                </div>
            </div>

            <table class='table'>
                <thead>
                <th style='width:90px'>类型</th>
                <th style='width:100px'>字段名称</th>
                <th style='width:50px'>必填</th>
                <th style='width:550px'>设置</th>
                <th></th>
                </thead>
                <tbody id="type-items">
                @if(!empty($dfields))
                    @foreach($dfields as $k1 => $v1)
                        <?php $data_type = $v1['data_type'];?>
                        @if($datacount>0)
                            {{$flag=2}}
                        @endif

                        @include('Yunshop\Diyform::admin.tpl.tpl')
                        <?php $kw++;?>
                    @endforeach
                @endif
                </tbody>
                <tr>
                    <td colspan='5'>
                        <div class='input-group'>
                            <select id="data_type" name="data_type" class="form-control" style="width:200px;">
                                @foreach($data_type_config as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach

                            </select>
                            <div class='input-group-btn'>
                                <a class="btn btn-primary btn-add-type" href="javascript:;" onclick="addType();"><i
                                            id="add_field" class="fa fa-plus" title=""></i> 增加一个字段</a>
                            </div>
                        </div>
                    </td>
                </tr>

            </table>
        </div>
        <div class="form-group">

            <div class="col-sm-9 col-xs-12">
                <input type="submit" value="提交" class="btn btn-primary"/>
                <a href="{{yzUrl("plugin.diyform.admin.diyform.manage")}}"><span class="btn btn-default"
                                                                           style='margin-left:10px;'>返回列表</span></a>
            </div>
        </div>

    </form>
</div>
<div style="width:100%;height:150px;"></div>
@include('Yunshop\Diyform::admin.tpl.script')
<script language='javascript'>

    $('form').submit(function(){
        var check = true;
        $(".tp_title,.tp_name").each(function(){
            var val = $(this).val();
            if(!val){
                $(this).focus(),$('form').attr('stop',1),tip.msgbox.err('不能为空!');
                check =false;
                return false;
            }
        });

        if(kw == 0) {
            $(this).focus(),$('form').attr('stop',1),tip.msgbox.err('请先添加字段再提交!');
            check =false;
            return false;
        }

        if(!check){return false;}
        var o={}; // 判断重复

        if(!check){
            return false;
        }
        $('form').removeAttr('stop');
        return true;
    });

</script>
@endsection