@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form id="dataform" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="right-titpos">
                    <ul class="add-snav">
                        <li class="active">
                            <a href="#">
                                淘宝CSV上传
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class='alert alert-danger'>尽量在服务器空闲时间来操作，会占用大量内存与带宽，在获取过程中，请不要进行任何操作!</div>
                        <div class="alert alert-info">
                                功能介绍：可将淘宝助理以及其他途径获取的淘宝商品CSV文件快速上传至商城,节约您的大量时间!
                                <span>使用方法： 1. 将您获取到的CSV文件转存为Excel格式,否则将无法识别</span>
                                <span style="padding-left: 74px;">2. 将配套的图片文件包压缩为Zip格式压缩包并且导入(图片需在压缩包根目录下)</span>
                                <span style="padding-left: 74px;">3. 确认上传即可</span>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label must">EXCEL</label>
                            <div class="col-sm-5"  style="padding-right:0;">
                                <input type="file" name="send[excel_file]" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label must">ZIP</label>
                            <div class="col-sm-5"  style="padding-right:0;">
                                <input type="file" name="send[zip_file]" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="modal-footer">
                        <input type="submit" value="确认导入" class="btn btn-primary"/>
                        <a href="{{$excel_url}}"><input type="button" value="Excel示例文件下载" class="btn btn-primary"/></a>
                        <a href="{{$zip_url}}"><input type="button" value="Zip示例文件下载" class="btn btn-primary"/></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
    </script>
@endsection

