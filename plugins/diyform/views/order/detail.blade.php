@extends('layouts.base')
<link rel="stylesheet" href="{{static_url('css/viewer.min.css')}}">

@section('title','商品自定义表单详情')

@section('content')

    <div class="w1200 m0a">
        <div  class="main">
            <form method="post" action="" class="form-horizontal form">
                <div class="rightlist">
                    <div class="right-titpos">
                        <ul class="add-snav">
                            <li class="active"><a href="#" onclick="javascript:history.back(-1);">&#60;&#60;返回订单</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel panel-default" id="tp_name_0">
                        @foreach($data as $k => $v)
                            <span class="help-block">商品Id：{{$v['goods_id']}}</span>
                          @foreach($v['data'] as $fname => $field)


                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                    {{$v['form']['fields1'][$fname]['tp_name']}}
                                </label>
                                <div class="col-sm-9 col-xs-12">
                                                <span class="help-block">
                                                     @if(is_array($field) && (strexists($field[0], 'image') || strexists($field[0], 'images') || strexists($field[0], 'newimage')))
                                                        @foreach($field as $k1=>$v1)
                                                            <img style="width:50px;height:50px;border:1px solid #ccc;padding:1px"
                                                                 src="{!! yz_tomedia($v1) !!}"/>
                                                        @endforeach
                                                    @elseif (is_array($field) && (!strexists($field[0], 'image') || !strexists($field[0], 'images') || !strexists($field[0], 'newimage')))
                                                        @foreach($field as $v1)
                                                            <span>{{ $v1 }}</span>
                                                        @endforeach
                                                    @else
                                                        {{$field}}
                                                    @endif
                                                </span>
                                </div>
                            </div>


                            @endforeach
                        @endforeach
                    </div>

                    <div class="panel panel-footer">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript" src="{{static_url('js/viewer.min.js')}}"></script>
    <script>
        var tp_name_0 = new Viewer(document.getElementById('tp_name_0'), {
            url: 'src'
        });
    </script>
@endsection('content')
