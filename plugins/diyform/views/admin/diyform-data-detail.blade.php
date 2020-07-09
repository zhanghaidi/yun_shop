@extends('layouts.base')

@section('title','申请流程详情')

@section('content')

    <div class="w1200 m0a">
        <div  class="main">
            <form method="post" action="" class="form-horizontal form">
                <div class="rightlist">
                    <div class="right-titpos">
                        <ul class="add-snav">
                            <li class="active"><a href="{{yzWebUrl('plugin.diyform.admin.diyform-data.get-form-data',['id'=> $formId])}}" style="color: blue">&#60;&#60;返回列表</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel panel-default">
                            @foreach($fields as $fname => $field)
                                @foreach($item['form_data'] as $key => $val)
                                    @if($key == $fname)
                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                                {{$field['tp_name']}}
                                            </label>
                                            <div class="col-sm-9 col-xs-12">
                                                <span class="help-block">
                                                   @if(is_array($val) && (strexists($val[0], 'image') || strexists($val[0], 'images') || strexists($val[0], 'newimage')))
                                                        @foreach($val as $v)
                                                            <img style="width:50px;height:50px;border:1px solid #ccc;padding:1px"
                                                                 src="{!! yz_tomedia($v) !!}"/>
                                                        @endforeach
                                                    @elseif (is_array($val) && (!strexists($val[0], 'image') || !strexists($val[0], 'images') || !strexists($val[0], 'newimage')))
                                                        @foreach($val as $v)
                                                            <span>{{ $v }}</span>
                                                        @endforeach
                                                    @else
                                                        {{$val}}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                    </div>

                    <div class="panel panel-footer">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection('content')

