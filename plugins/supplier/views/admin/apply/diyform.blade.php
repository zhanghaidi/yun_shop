@extends('layouts.base')
<link rel="stylesheet" href="{{static_url('css/viewer.min.css')}}">

@section('content')
    <div class="rightlist">
        <form action="" method='post' class='form-horizontal'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <span>详细信息</span>
                </div>
                <div class='panel-body' id="tp_name_0">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
                        <div class="col-sm-9 col-xs-12">
                            <img src='{{$supplier->hasOneMember->avatar}}'
                                 style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                            {{$supplier->hasOneMember->nickname}}
                        </div>
                    </div>

                    @foreach($fields as $field_key => $field)
                        @if($field['data_type'] != 99)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$field['tp_name']}}</label>
                            <div class="col-sm-9 col-xs-12">
                                @if(!is_array($form_data[$field_key]))
                                    {{$form_data[$field_key]}}
                                @else
                                    @foreach($form_data[$field_key] as $row)
                                        <img src='{{yz_tomedia($row)}}'
                                             style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="button" name="back" onclick='history.back()'  value="返回列表" class="btn btn-default" />
                        </div>
                    </div>
                </div>
        </form>
    </div>
<script type="text/javascript" src="{{static_url('js/viewer.min.js')}}"></script>
<script>
    var tp_name_0 = new Viewer(document.getElementById('tp_name_0'), {
        url: 'src'
    });
</script>
@endsection