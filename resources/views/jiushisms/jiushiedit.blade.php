@extends('layouts.base')
@section('title', '商品表单')
@section('content')

    <div class="rightlist">
        @include('layouts.tabs')
        <form action="{{ yzWebUrl('jiushisms.jiushisms.jiushiedit') }}" method="post"
              class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>

                <div class='panel panel-default'>
                    <div class='panel-heading'>填写短信内容</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">灸师真实姓名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input name="id" type="hidden" class="form-control" value="{{ $info['id'] }}"/>
                                <input type="text" name="jiushi_name" class="form-control"
                                       value="{{$info['jiushi_name']}}" placeholder="请输入灸师真实姓名"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">灸师微信号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="jiushi_wechat" class="form-control"
                                       value="{{$info['jiushi_wechat']}}" placeholder="请输入灸师企业微信号"/>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="保存"
                               class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

