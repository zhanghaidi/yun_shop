@extends('layouts.base')

@section('content')
@section('title', trans('添加保险公司'))
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">添加保险公司</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="id" class="form-control" value="{{$data['id']}}"/>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>排序</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="form[sort]" class="form-control"
                                   value="{{$data['sort']}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>保险公司名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="form[name]" class="form-control" value="{{$data['name']}}"/>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='form[is_show]' value='1'
                                       @if($data['is_show'] == 1) checked @endif
                                /> 显示
                            </label>
                            <label class='radio-inline' style="margin-left: 55px;">
                                <input type='radio' name='form[is_show]' value='0'
                                       @if($data['is_show'] == 0) checked @endif
                                /> 不显示
                            </label>
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"
                                   onclick="return formcheck()"/>
                            <input type="button" name="back" onclick='history.back()' style=''
                                   value="返回列表"
                                   class="btn btn-default back"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
<script type="text/javascript">
    function formcheck() {
        var shuzi = /^\d+$/;
        if (!shuzi.test($(':input[name="form[sort]"]').val())) {
            Tip.focus(':input[name="form[sort]"]', "必须为数字");
            return false;
        }
        if ($(':input[name="form[name]"]').val() == '') {
            Tip.focus(':input[name="form[name]"]', "请输入公司名称");
            return false;
        }

        return true;
    }
</script>
@endsection

