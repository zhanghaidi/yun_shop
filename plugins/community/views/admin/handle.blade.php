@extends('layouts.base')
@section('title','数据同步')

@section('content')
    <div class="page-heading">
        <h2>数据同步</h2>
    </div>
    {{--<div class="alert alert-info">
        功能介绍: 同步会员等级到圈子社区中
        <span style="padding-left: 60px;">如重复导入数据将以最新导入数据为准，请谨慎使用</span>
        <span style="padding-left: 60px;">数据导入订单状态自动修改为已发货</span>
        <span style="padding-left: 60px;">一次导入的数据不要太多,大量数据请分批导入,建议在服务器负载低的时候进行</span>
        <br>
        使用方法: <span style="padding-left: 60px;">1. 下载Excel模板文件并录入信息</span>
        <span style="padding-left: 60px;">2. 选择快递公司</span>
        <span style="padding-left: 60px;">3. 上传Excel导入</span>
        <br>
        格式要求：  Excel第一列必须为订单编号，第二列必须为快递单号，请确认订单编号与快递单号的备注
    </div>--}}

    <form id="importform" class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
        <div class='form-group'>
            <div class="col-sm-12">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-block btn-success" name="handle" value="同步">同步</button>
                </div>
            </div>
        </div>
    </form>
@endsection('content')

