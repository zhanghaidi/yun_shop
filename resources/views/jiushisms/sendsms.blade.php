@extends('layouts.base')
@section('title', '商品表单')
@section('content')

    <div class="rightlist">
        @include('layouts.tabs')
        <form action="{{ yzWebUrl('jiushisms.jiushisms.sendsms') }}" method="post"
              class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>

                <div class='panel panel-default'>
                    <div class='panel-heading'>填写短信内容</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="phone_num" class="form-control" placeholder="请输入客户手机号"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">灸师微信号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="jiushi_WeChat" class="form-control" placeholder="请输入灸师企业微信号"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信内容</label>
                            <div class="col-sm-9 col-xs-12">
                                <textarea name="sms_content" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <span>ps：短信消息体内容</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="发送短信"
                               class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

